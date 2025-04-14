# Umfrage - PHP Survey System

This README describes the installation and configuration of the Umfrage system, a PHP-based survey application.

## Overview

Umfrage is a comprehensive PHP survey application that offers the following features:
- Survey creation and management
- Various question types (multiple choice, scale, open text, etc.)
- User management and access control
- Result collection and analysis
- Statistical evaluation and reporting
- Export functionality for survey data
- Responsive design

## System Requirements

- Docker and Docker Compose
- Free ports: 80, 8081

## Installation and Launch

1. Clone this repository to your local computer
2. Navigate to the Umfrage directory
3. Start the application with Docker Compose:

```bash
docker-compose up -d
```

## Accessing the Application

After successful launch, the following services are available:

- **Survey application**: http://localhost
- **Traefik dashboard**: http://localhost:8081
- **Web server**: http://web.localhost
- **phpMyAdmin**: http://db.localhost

## Components

The application consists of the following Docker containers:

- **umfrage**: PHP application container with the Umfrage system
- **webserver**: NGINX web server for static content
- **traefik**: Reverse proxy for routing the various services
- **mysql**: Database server (MySQL 5.7)
- **phpmyadmin**: Web-based database management

## Configuration

### Database

The MySQL database is configured with the following credentials:
- **Database**: umfrage
- **User**: umfrage
- **Password**: umfrage
- **Root password**: root

The database data is persistently stored in the `./mysql` directory.

### Survey Configuration

The main configuration file of the survey system is unfWerte.php, which is mounted as a volume into the container:

```yaml
volumes:
  - ./umfrage/unfWerte.php:/var/www/html/unfWerte.php:rw
```

It can be edited directly in the host system, and the changes will take effect immediately in the container.

> Make sure to set the correct permissions (`chmod 777 umfrage/unfWerte.php`) for the file to be writable by the container.

### Network

The application uses an isolated Docker network with the following configuration:
- **Network name**: umfrage
- **Subnet**: 172.106.0.0/24
- Each service has a fixed IP address in the subnet

## Troubleshooting

The following commands can be helpful for troubleshooting:

- View container logs: `docker-compose logs -f umfrage`
- Restart all containers: `docker-compose restart`
- Completely rebuild the system: `docker-compose down -v && docker-compose up -d`

## Directories

The most important data is located in the following directories:
- umfrage: PHP files of the survey system
- `./mysql`: Database files (remove this folder to reset the database)
- `./web`: here you can place your own files. See `http://web.localhost` for more information

These should be backed up regularly.
