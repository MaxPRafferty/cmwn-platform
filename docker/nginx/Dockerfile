FROM ubuntu:14.04
MAINTAINER Chuck "MANCHUCK" Reeves <chuck@manchuck.com>

RUN apt-get update && apt-get install -y \
    nginx openssl

COPY entrypoint.sh /opt/entrypoint.sh

RUN chmod a+x /opt/entrypoint.sh
RUN /opt/entrypoint.sh

COPY nginx.conf /etc/nginx/nginx.conf

EXPOSE 80 443

CMD ["nginx", "-g", "daemon off;"]