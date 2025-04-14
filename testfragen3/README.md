# Testfragen3 - PHP Quiz System

This README describes the installation and configuration of the Testfragen3 system, a PHP-based quiz and testing application.

## Overview

Testfragen3 is a comprehensive PHP quiz application that offers the following features:
- Question management with multiple formats (multiple choice, free text, etc.)
- Test creation and organization
- User accounts for students and teachers
- Automatic and manual grading
- Performance tracking and analytics
- Export functionality for results
- Responsive design

## System Requirements

- Docker and Docker Compose
- Free ports: 80, 8081

## Installation and Launch

1. Clone this repository to your local computer
2. Navigate to the Testfragen3 directory
3. Start the application with Docker Compose:

```bash
docker-compose up -d
```

## Accessing the Application

After successful launch, the following services are available:

- **Quiz application**: http://localhost
- **Traefik dashboard**: http://localhost:8081
- **Web server**: http://web.localhost
- **phpMyAdmin**: http://db.localhost

## Components

The application consists of the following Docker containers:

- **testfragen**: PHP application container with the Testfragen3 system
- **webserver**: NGINX web server for static content
- **traefik**: Reverse proxy for routing the various services
- **mysql**: Database server (MySQL 5.7)
- **phpmyadmin**: Web-based database management

## Configuration

### Database

The MySQL database is configured with the following credentials:
- **Database**: testfragen
- **User**: testfragen
- **Password**: testfragen
- **Root password**: root

The database data is persistently stored in the `./mysql` directory.

### Quiz Configuration

The main configuration file of the quiz system is fraWerte.php, which is mounted as a volume into the container:

```yaml
volumes:
  - ./testfragen/fraWerte.php:/var/www/html/fraWerte.php:rw
```

It can be edited directly in the host system, and the changes will take effect immediately in the container.

> Make sure to set the correct permissions (`chmod 777 testfragen/fraWerte.php`) for the file to be writable by the container.

### Network

The application uses an isolated Docker network with the following configuration:
- **Network name**: testfragen
- **Subnet**: 172.105.0.0/24
- Each service has a fixed IP address in the subnet

## Troubleshooting

The following commands can be helpful for troubleshooting:

- View container logs: `docker-compose logs -f testfragen`
- Restart all containers: `docker-compose restart`
- Completely rebuild the system: `docker-compose down -v && docker-compose up -d`

## Directories

The most important data is located in the following directories:
- `./testfragen`: PHP files of the quiz system
- `./mysql`: Database files (remove this folder to reset the database)
- `./web`: here you can place your own files. See `http://web.localhost` for more information

These should be backed up regularly.