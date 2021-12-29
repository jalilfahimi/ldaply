FROM ubuntu 
ENV DEBIAN_FRONTEND=noninteractive 
RUN apt-get update 
RUN apt -y install software-properties-common
RUN apt-get install -y apache2 
RUN apt-get install -y apache2-utils

# Install php-7.4
RUN apt-get update
RUN apt-get install -y php7.4

# Install extensions
RUN apt-get install -y php7.4-apcu php7.4-mysql php7.4-XML php7.4-mbstring php7.4-curl php7.4-zip php7.4-gd php7.4-intl php7.4-xmlrpc php7.4-soap php7.4-ldap php7.4-bcmath php7.4-gmp

# Install xdebug
RUN apt-get install php7.4-xdebug
RUN apt-get clean 

# Virtual host configuration
COPY ./virtualhost /etc/apache2/sites-available/dssimplesamlphp.conf
RUN a2ensite dssimplesamlphp.conf

# Custom php.ini
COPY ./php.ini /etc/php/7.4/apache2/php.ini

# Apache custom port configuration (port:40022)
COPY ./ports.conf /etc/apache2/ports.conf

# Cron
RUN apt-get update && apt-get install cron

# Postfix
RUN apt-get update 
RUN apt-get install -y postfix

#########################################################################################
# Start
EXPOSE 40022 
ADD start.sh /
RUN chmod +x /start.sh

CMD ["/start.sh"]
