# Laravel API for HAProxy

# Requirements 

* Composer
* HAProxy
* PHP >= 5.5.9
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension
* Tokenizer PHP Extension

# Instalation

## Laravel Load Balancer (LLB)

## 1st step download the system

    mkdir -p /opt/LLB/ && cd /opt/LLB/
    git clone https://github.com/aa211/haproxy-api-laravel-5.2

## 2nd step 

[Install composer](https://getcomposer.org/download/)

  Install the laravel framework
  
    php composer.phar update

## 3rd step

Start the server with:
  
    php artisan serve --host 0.0.0.0 --port 9000
  
## 4th step

Install the HAProxy

Centos:
  
    yum install haproxy

Ubuntu:

    apt-get install haproxy
  
# Usage and examples

  Post values to ``http://<server-ip-address/sever-dns>/set/configuration``:
  
    Config=<<Config To load (name ex: http, ftp, ETC) [Obligatory]>>
    
    SecureName=<< Name for https [Obligatory for HTTPS] >>
    
    Name=<< Name for the service [Obligatory] >>
  
  
  If need to 1 server in banckend:
  
    IP= << ip address of server in backend [Obligatory] >>
    
    Port= << port address of server in backend [Obligatory] >>
    
    SecureIP= << ip address of server in backend for HTTPS [Obligatory for HTTPS] >>
    
    SecurePort= << port address of server in backend for HTTPS [Obligatory for HTTPS]>>
  
  
  Or multiple:
  
    IP[]=<< ip address of server in backend [Obligatory] >>
    
    Port[]=<< port address of server in backend [Obligatory] >>
    
    IP[]=<< ip address of server in backend [Obligatory] >>
    
    Port[]=<< port address of server in backend [Obligatory] >>
    
    SecureIP[]=<< ip address of server in backend for HTTPS [Obligatory for HTTPS] >>
    
    SecurePort[]=<< port address of server in backend for HTTPS [Obligatory for HTTPS] >>
    
    SecureIP[]=<< ip address of server in backend for HTTPS [Obligatory for HTTPS] >>
    
    SecurePort[]=<< port address of server in backend for HTTPS [Obligatory for HTTPS]>>
  
  
  
    LocalPort=<< Port to be used for starting load balancing >>
    
    LocalSecurePort=<< Port to be used for starting load balancing for HTTPS >>
    
## Example 
    
       curl -X POST \
        -F "Config=p" \
        -F "SecureName=webs" \
        -F "Name=web" \
        -F "IP[]=127.0.0.1" \
        -F "Port[]=8000" \
        -F "IP[]=127.0.0.1" \
        -F "Port[]=8001" \
        -F "SecureIP[]=127.0.0.1" \
        -F "SecurePort[]=1443" \
        -F "SecureIP[]=127.0.0.1" \
        -F "SecurePort[]=1444" \
        -F "LocalPort=80" \
        -F "LocalSecurePort=443 \
        -F "LocalPort=80" \
        -F "LocalSecurePort=443" \
        http://10.0.0.53:3000/set/configuration 
