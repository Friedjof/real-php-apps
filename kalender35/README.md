# Kalender35 - PHP Calendar System

This README describes the installation and configuration of the Kalender35 system, a PHP-based calendar application.

## Overview

Kalender35 is a comprehensive PHP calendar application that offers the following features:
- Appointment management with detailed view
- Month, week, and list views
- Reminder function
- Notification system
- User management
- iCal export
- Responsive design

## System Requirements

- Docker and Docker Compose
- Free ports: 80, 8081

## Installation and Launch

1. Clone this repository to your local computer
2. Navigate to the Kalender35 directory
3. Start the application with Docker Compose:

```bash
docker-compose up -d
```

## Accessing the Application

After successful launch, the following services are available:

- **Calendar application**: http://localhost
- **Traefik dashboard**: http://localhost:8081
- **Web server**: http://web.localhost
- **phpMyAdmin**: http://db.localhost

## Components

The application consists of the following Docker containers:

- **kalender**: PHP application container with the Kalender35 system
- **webserver**: NGINX web server for static content
- **traefik**: Reverse proxy for routing the various services
- **mysql**: Database server (MySQL 5.7)
- **phpmyadmin**: Web-based database management

## Configuration

### Database

The MySQL database is configured with the following credentials:
- **Database**: kalender
- **User**: kalender
- **Password**: kalender
- **Root password**: root

The database data is persistently stored in the `./mysql` directory.

### Calendar Configuration

The main configuration file of the calendar is kalWerte.php, which is mounted as a volume into the container:

```yaml
volumes:
  - ./kalender/kalWerte.php:/var/www/html/kalWerte.php:rw
```

It can be edited directly in the host system, and the changes will take effect immediately in the container.

> Make shure to set the correct permissions (`chmod 777 kalender/kalWerte.php`) for the file to be writable by the container.

### Network

The application uses an isolated Docker network with the following configuration:
- **Network name**: kalender
- **Subnet**: 172.103.0.0/24
- Each service has a fixed IP address in the subnet

## Troubleshooting

The following commands can be helpful for troubleshooting:

- View container logs: `docker-compose logs -f kalender`
- Restart all containers: `docker-compose restart`
- Completely rebuild the system: `docker-compose down -v && docker-compose up -d`

## Directories

The most important data is located in the following directories:
- `./kalender`: PHP files of the calendar system
- `./mysql`: Database files (remove this folder to reset the database)
- `./web`: here you can place your own files. See `http://web.localhost` for more information
- `./kalender/kalWerte.php`: Calendar configuration

These should be backed up regularly.