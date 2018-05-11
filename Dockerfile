FROM  daocloud.io/1514582970/pms_docker_php:cli71_swoole_phalcon

MAINTAINER      Dongasai "1514582970@qq.com"

RUN apt update;apt install -y vim
COPY . /var/www/html/
ENV APP_SECRET_KEY=123456
ENV APP_HOST_IP=192.168.1.220
ENV APP_HOST_PORT=34601

ENV REGISTER_SECRET_KEY=123456
ENV REGISTER_ADDRESS=123456
ENV REGISTER_PORT=9502


ENV GCACHE_HOST=192.168.1.220
ENV GCACHE_PORT=6379
ENV GCACHE_AUTH=0
ENV GCACHE_PERSISTENT=0
ENV GCACHE_PREFIX=cms
ENV GCACHE_INDEX=1

ENV SESSION_CACHE_HOST=192.168.1.220
ENV SESSION_CACHE_PORT=6379
ENV SESSION_CACHE_AUTH=0
ENV SESSION_CACHE_PERSISTENT=1
ENV SESSION_CACHE_PREFIX=session
ENV SESSION_CACHE_INDEX=2


ENV MYSQL_HOST=192.168.1.220
ENV MYSQL_PORT=3306
ENV MYSQL_DBNAME=user
ENV MYSQL_PASSWORD=123456
ENV MYSQL_USERNAME=user

EXPOSE 9502
WORKDIR /var/www/html/
RUN composer install
CMD php start/start.php