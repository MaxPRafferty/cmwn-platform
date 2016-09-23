#!/usr/bin/env bash

AWS_CMD=$(which aws)

if [ "$AWS_CMD" = "" ]; then
    echo "In order to deploy, you must have the aws cli installed and configured"
    exit 1
fi

BASTION=$($AWS_CMD ec2 describe-instances \
  --query 'Reservations[*].Instances[*].[InstanceId]' \
  --output text \
  --filter "Name=tag-key,Values=Name" "Name=tag-value,Values=Bastion")

if [ "$AWS_CMD" = "" ]; then
    echo "In order to deploy, bastion needs to be up"
    exit 1
fi

# Set some defaults
AMI="ami-6869aa05";
SECURITY_GROUP="sg-079af97c"
SUBNET="subnet-b8094ce0"
VERBOSE=5

declare -a LOG_LEVELS
# https://en.wikipedia.org/wiki/Syslog#Severity_level
LOG_LEVELS=([0]="emerg" [1]="alert" [2]="crit" [3]="err" [4]="warning" [5]="notice" [6]="info" [7]="debug")

## Set required variables
VERSION=$1
shift

APP="$(echo ${1:0:1} | tr "[a-z]" "[A-Z]")${1:1}"
shift

ENV="$(echo ${1:0:1} | tr "[a-z]" "[A-Z]")${1:1}"
shift

for i in "$@"
do
    case $i in
        -v|--verbose)
        VERBOSE=7
        shift
        ;;
        -i=*|--instance=*)
        INSTANCE="${i#*=}"
        shift
        ;;
        -h|--help)
        echo "This is the help"
        exit 0
        shift
        ;;
        *)
                # unknown option
        ;;
    esac
    shift
done

function .log () {
    local LEVEL=${1}
    shift
    if [ ${VERBOSE} -ge ${LEVEL} ]; then
        echo "$@"
    fi
}

if [ "$VERSION" == "" ]; then
    echo "Invalid version"
    exit 1
fi

if [ "$APP" == "" ]; then
    echo "Invalid Application"
    exit 1
fi

if [ "$ENV" == "" ]; then
    echo "Invalid Environment"
    exit 1
fi

.log 7 "Currently the instance id is: $INSTANCE"
.log 5 "Deploying $VERSION of $APP to $ENV"
.log 7 "Starting instance using $AMI on subnet $SUBNET in security group $SECURITY_GROUP"

if [ "$INSTANCE" == "" ]; then
    .log 6 "Creating AWS instance"
# aws ec2 run-instances --image-id ami-xxxxxxxx --count 1 --instance-type t1.micro --key-name MyKeyPair --security-group-ids sg-xxxxxxxx --subnet-id subnet-xxxxxxxx
    INSTANCE=$("$AWS_CMD" ec2 run-instances --image-id "$AMI" \
        --count 1 \
        --instance-type "t2.micro" \
        --key-name cmwn \
        --security-group-ids "$SECURITY_GROUP" \
        --subnet-id "$SUBNET" \
        --output text | awk -F"\t" '$1=="INSTANCES" {print $8}')

    .log 7 "Created build instance $INSTANCE"
fi

.log 6 "Tagging instance $INSTANCE"
aws ec2 create-tags --resources $INSTANCE --tags Key=Name,Value="$APP $VERSION Deploy"
aws ec2 create-tags --resources $INSTANCE --tags Key=Type,Value="$APP Build"
aws ec2 create-tags --resources $INSTANCE --tags Key=Web,Value=""
aws ec2 create-tags --resources $INSTANCE --tags Key=Php,Value=""
aws ec2 create-tags --resources $INSTANCE --tags Key=Environment,Value="$ENV"

NEXT_WAIT_TIME=0
TOTAL_WAIT_TIME=5

.log 6 "Waiting for instance to start"
while true
do
    STATUS=$($AWS_CMD ec2 describe-instance-status --instance-ids=$INSTANCE | awk -F"\t" '$1=="INSTANCESTATE" {print $3}')

    .log 7 "Current status: $STATUS"
    if [ "$STATUS" == "running" ]
    then
        .log 7 "Instance is in running state"
        break
    fi

    if [ $NEXT_WAIT_TIME -eq $TOTAL_WAIT_TIME ]; then
        .log 1 "Instance never started"
        exit 1
    fi

    PROGRESS = $TOTAL_WAIT_TIME / $NEXT_WAIT_TIME
    ((NEXT_WAIT_TIME++))
    sleep 5
done

echo "Instance is running"

COMMAND_ID=$(aws ssm send-command \
    --instance-ids "$BASTION" \
    --document-name "AWS-RunShellScript" \
    --parameters commands='ls -al',workingDirectory='/etc/ansible' \
    --output text \
    --query "Command.CommandId")

echo $COMMAND_ID