FROM ubuntu:14.04
MAINTAINER Chuck "MANCHUCK" Reeves <chuck@manchuck.com>

RUN apt-get update && apt-get install -y \
    nginx

COPY nginx.conf /etc/nginx/nginx.conf

EXPOSE 443

CMD ["nginx", "-g", "daemon off;"]