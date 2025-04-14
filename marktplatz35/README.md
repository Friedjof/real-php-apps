# Marktplatz35 - PHP-based Marketplace System

This README describes the installation and configuration of the Marktplatz35 system, a PHP-based marketplace application.

## Overview

Marktplatz35 is a comprehensive PHP marketplace application that offers the following features:
- Product listing with detailed view
- Category and search functionality
- User accounts and profiles
- Shopping cart and checkout process
- Order management
- Seller and buyer interfaces
- Responsive design

## System Requirements

- Docker and Docker Compose
- Free ports: 80, 8081

## Installation and Launch

1. Clone this repository to your local computer
2. Navigate to the Marktplatz35 directory
3. Start the application with Docker Compose:

```bash
docker-compose up -d
```

## Accessing the Application

After successful launch, the following services are available:

- **Marketplace application**: http://localhost
- **Traefik dashboard**: http://localhost:8081
- **Web server**: http://web.localhost
- **phpMyAdmin**: http://db.localhost

## Components

The application consists of the following Docker containers:

- **marktplatz**: PHP application container with the Marktplatz35 system
- **webserver**: NGINX web server for static content
- **traefik**: Reverse proxy for routing the various services
- **mysql**: Database server (MySQL 5.7)
- **phpmyadmin**: Web-based database management

## Configuration

### Database

The MySQL database is configured with the following credentials:
- **Database**: marktplatz
- **User**: marktplatz
- **Password**: marktplatz
- **Root password**: root

The database data is persistently stored in the `./mysql` directory.

### Marketplace Configuration

The main configuration file of the marketplace is marktplatzWerte.php, which is mounted as a volume into the container:

```yaml
volumes:
  - ./marktplatz/marktplatzWerte.php:/var/www/html/marktplatzWerte.php:rw
```

It can be edited directly in the host system, and the changes will take effect immediately in the container.

> Make sure to set the correct permissions (`chmod 777 marktplatz/marktplatzWerte.php`) for the file to be writable by the container.

### Network

The application uses an isolated Docker network with the following configuration:
- **Network name**: marktplatz
- **Subnet**: 172.103.0.0/24
- Each service has a fixed IP address in the subnet

## Troubleshooting

The following commands can be helpful for troubleshooting:

- View container logs: `docker-compose logs -f marktplatz`
- Restart all containers: `docker-compose restart`
- Completely rebuild the system: `docker-compose down -v && docker-compose up -d`

## Directories

The most important data is located in the following directories:
- `./marktplatz`: PHP files of the marketplace system
- `./mysql`: Database files (remove this folder to reset the database)
- `./web`: here you can place your own files. See `http://web.localhost` for more information
