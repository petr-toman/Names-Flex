FROM centos:centos7
#docker build -t names-flex .
#backgroud  docker run -d --rm -p 80:80 -v $(pwd):/var/www/html --name=toman names-flex
# -----------------------------------------------------------------------------
#  Basic settings + some utilities
# -----------------------------------------------------------------------------
RUN yum -y update && \
    yum -y install \
	yum-utils \
        epel-release \
        unzip \
		vim \
		mc \
		cronie

# -----------------------------------------------------------------------------
#  APACHE + PHP 5.6 (from webtatic - https://webtatic.com/packages/php56/)
# -----------------------------------------------------------------------------
RUN yum -y update && \
  	rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm &&\
	yum -y install httpd \
				php56w \
			    php56w-opcache \
			    php56w-mysql \
		        php56w-pecl-apcu \
			  	php56w-bcmath	\
			  	php56w-mbstring	\
			  	php56w-mcrypt \
			  	php56w-odbc \
			  	php56w-snmp \
			  	php56w-soap \
			  	php56w-xmlrpc \
			  	php56w-dba \
			  	php56w-gd \
			  	php56w-ldap
# -----------------------------------------------------------------------------
# Set static config
# -----------------------------------------------------------------------------
#COPY php.ini /etc/php.ini
ENV TZ=Europe/Prague
RUN  date
#COPY startup.sh /startup.sh
#RUN chmod 0711 /startup.sh
#ENTRYPOINT ["bin/bash", "./startup.sh"]

# -----------------------------------------------------------------------------
# Set ports and env variable HOME
# -----------------------------------------------------------------------------
EXPOSE 80
VOLUME /var/www/html
VOLUME /var/log/httpd

# -----------------------------------------------------------------------------
# Start
# -----------------------------------------------------------------------------
CMD ["/usr/sbin/httpd", "-DFOREGROUND"]