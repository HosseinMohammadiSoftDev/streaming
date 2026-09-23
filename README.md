# Secure Video Streaming & DRM

A secure video streaming platform built with **Laravel, FFmpeg, encryption, and a dedicated video player**.

The project is designed to protect video content throughout the processing and delivery pipeline. Instead of exposing the original video file directly to the client, the system processes, encrypts, packages, and delivers protected media through a controlled streaming workflow.

---

## Overview

The platform provides a complete pipeline for protected video delivery:

```text
                    ┌─────────────────────┐
                    │     Original Video  │
                    └──────────┬──────────┘
                               │
                               ▼
                    ┌─────────────────────┐
                    │    FFmpeg Pipeline  │
                    │                     │
                    │ Transcoding          │
                    │ Segmentation         │
                    │ Packaging            │
                    │ Encryption           │
                    └──────────┬──────────┘
                               │
                               ▼
                    ┌─────────────────────┐
                    │ Protected Media     │
                    │ HLS / DASH          │
                    │ Encrypted Segments  │
                    └──────────┬──────────┘
                               │
                               ▼
                    ┌─────────────────────┐
                    │ Authorization Layer │
                    │                     │
                    │ Authentication      │
                    │ Permissions         │
                    │ Access Control      │
                    └──────────┬──────────┘
                               │
                               ▼
                    ┌─────────────────────┐
                    │   Custom Player     │
                    │                     │
                    │ Secure Playback     │
                    │ Stream Management   │
                    └─────────────────────┘
```

The architecture separates **media processing**, **content protection**, **authorization**, **storage**, and **playback** so that protected media is not treated as a normal publicly accessible file.

---

## Core Features

* Protected video streaming
* FFmpeg-based media processing
* Video transcoding
* Media segmentation
* HLS/DASH streaming workflows
* Encrypted media delivery
* Encryption key management
* Controlled access to protected content
* API authentication with Laravel Sanctum
* Role and permission management
* S3-compatible media storage
* Modular Laravel architecture
* Dedicated video player
* Streaming-oriented backend APIs

---

## Security Model

The main security principle of the project is:

> **The original media file should never be considered a publicly accessible playback resource.**

Instead, the video follows a protected pipeline:

```text
Original Media
      │
      ▼
   FFmpeg
      │
      ├── Transcoding
      │
      ├── Segmentation
      │
      ├── Packaging
      │
      └── Encryption
      │
      ▼
Encrypted Media
      │
      ▼
Authorization
      │
      ▼
Protected Stream
      │
      ▼
Custom Player
```

This architecture makes it possible to apply access control before allowing a client to obtain the information required for playback.

---

# Media Processing

FFmpeg is used as the media-processing engine.

The project integrates:

* `php-ffmpeg/php-ffmpeg`
* `aminyazdanpanah/php-ffmpeg-video-streaming`

The streaming library provides functionality around HLS/DASH packaging, encryption and media processing.

Typical processing stages include:

```text
Upload
  │
  ▼
Validation
  │
  ▼
FFmpeg Processing
  │
  ├── Codec processing
  ├── Resolution processing
  ├── Bitrate processing
  └── Audio/Video processing
  │
  ▼
Segmentation
  │
  ▼
Encryption
  │
  ▼
Streaming Package
```

---

# Encryption

The system uses media encryption as one of the protection layers.

Encryption prevents the generated media segments from being directly usable as normal video files without the required decryption information.

For HLS-based workflows, AES encryption can be used during media packaging. The underlying streaming library supports encrypted HLS workflows and key rotation.

Conceptually:

```text
Video Segment
     │
     ▼
Encryption Algorithm
     │
     ├── Encryption Key
     ├── Initialization Data
     └── Encrypted Segment
             │
             ▼
        Protected Stream
```

The encryption layer is combined with authorization and controlled key delivery rather than relying on encryption alone.

---

# DRM Architecture

DRM in this project should be understood as a combination of several security mechanisms:

```text
                 ┌────────────────────┐
                 │     User / Client  │
                 └─────────┬──────────┘
                           │
                           ▼
                 ┌────────────────────┐
                 │ Authentication     │
                 └─────────┬──────────┘
                           │
                           ▼
                 ┌────────────────────┐
                 │ Authorization      │
                 └─────────┬──────────┘
                           │
                           ▼
                 ┌────────────────────┐
                 │ Stream Permission  │
                 └─────────┬──────────┘
                           │
                 ┌─────────┴──────────┐
                 │                    │
                 ▼                    ▼
          Protected Manifest      Key / License
                 │                    │
                 └─────────┬──────────┘
                           ▼
                 ┌────────────────────┐
                 │   Custom Player    │
                 └─────────┬──────────┘
                           ▼
                 ┌────────────────────┐
                 │ Decrypted Playback │
                 └────────────────────┘
```

The exact strength of a DRM implementation depends on how the encryption keys, licenses, manifests and client-side playback are protected.

---

# Custom Video Player

The project includes its own dedicated player instead of relying only on a generic `<video>` element.

The player acts as the controlled playback layer between the protected media and the user.

```text
Backend
   │
   │ Protected Stream
   ▼
┌───────────────────────┐
│     Custom Player     │
│                       │
│  Stream Management    │
│  Playback Control     │
│  Protected Content   │
│  Error Handling       │
└───────────┬───────────┘
            │
            ▼
         Decoder
            │
            ▼
         Display
```

The player is responsible for consuming the streaming resources required by the backend and providing the playback interface.

---

# Authentication

Laravel Sanctum is used for API authentication.

```text
Client
  │
  ▼
Authentication
  │
  ▼
Access Token
  │
  ▼
Protected API
```

This allows the streaming API to distinguish between authenticated and unauthenticated clients.

---

# Authorization

Authentication alone does not determine whether a user is allowed to watch a particular video.

The authorization layer can determine:

* Whether the user owns the content
* Whether the user has access to the content
* Whether the content is available
* Whether the user's role permits playback
* Whether a protected stream can be requested

Spatie Laravel Permission is used for role and permission management.

```text
User
 │
 ▼
Role
 │
 ▼
Permissions
 │
 ▼
Content Access
```

---

# Storage

The application supports S3-compatible storage through Flysystem.

```text
                 ┌──────────────┐
                 │ Application  │
                 └──────┬───────┘
                        │
                        ▼
                 ┌──────────────┐
                 │ Storage Layer│
                 └──────┬───────┘
                        │
              ┌─────────┴─────────┐
              ▼                   ▼
          Local Disk          S3 Storage
```

Media storage can therefore be separated from the application server.

This is especially useful when large video files and generated streaming segments are involved.

---

# Modular Architecture

The application uses `nwidart/laravel-modules` and follows a modular Laravel structure.

```text
Modules/
   │
   ├── Module A
   │
   ├── Module B
   │
   ├── Module C
   │
   └── ...
```

The project also defines PSR-4 autoloading for both the Laravel application and modules:

```json
{
    "App\\": "app/",
    "Modules\\": "Modules/"
}
```

This keeps domain-specific functionality isolated from the Laravel core application.

---

# Technology Stack

| Component      | Technology                 |
| -------------- | -------------------------- |
| Backend        | Laravel 10                 |
| Language       | PHP 8.1+                   |
| Media Engine   | FFmpeg                     |
| PHP FFmpeg     | PHP-FFMpeg                 |
| Streaming      | PHP FFmpeg Video Streaming |
| Authentication | Laravel Sanctum            |
| Authorization  | Spatie Laravel Permission  |
| Storage        | Flysystem / S3             |
| Architecture   | Modular Monolith           |
| Testing        | PHPUnit                    |
| Code Style     | Laravel Pint               |
| Frontend Build | Vite                       |

These dependencies are defined in the project's current `composer.json`.

---

# Installation

## Requirements

Make sure the following are installed:

```text
PHP >= 8.1
Composer
FFmpeg
Database
Node.js / NPM
```

Clone the repository:

```bash
git clone https://github.com/HosseinMohammadiSoftDev/streaming.git

cd streaming
```

Install PHP dependencies:

```bash
composer install
```

Install frontend dependencies:

```bash
npm install
```

Create the environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Configure the database, storage and media-processing settings inside `.env`.

Run migrations:

```bash
php artisan migrate
```

Build frontend assets:

```bash
npm run build
```

Start Laravel:

```bash
php artisan serve
```

For development:

```bash
npm run dev
```

---

# FFmpeg

Verify that FFmpeg is available:

```bash
ffmpeg -version
```

The application relies on FFmpeg for media processing, therefore FFmpeg must be available to the environment running the processing pipeline.

---

# Processing Lifecycle

A typical video lifecycle can be represented as:

```text
                    Upload
                      │
                      ▼
               Original Video
                      │
                      ▼
                 Validation
                      │
                      ▼
                  FFmpeg
                      │
          ┌───────────┼───────────┐
          ▼           ▼           ▼
      Transcode   Segment      Package
          │           │           │
          └───────────┼───────────┘
                      ▼
                  Encryption
                      │
                      ▼
              Protected Media
                      │
                      ▼
                 Storage/CDN
                      │
                      ▼
                 Authorization
                      │
                      ▼
                Custom Player
                      │
                      ▼
                   Playback
```

---

# Security Layers

The project does not depend on a single security mechanism.

Instead, content protection is built from multiple layers:

### 1. Source Protection

The original video is kept outside of direct public playback.

### 2. Media Encryption

Generated media can be encrypted before delivery.

### 3. Segmentation

Video is distributed as streaming segments rather than a single downloadable media file.

### 4. Authentication

Only authenticated clients can access protected APIs.

### 5. Authorization

Access to individual protected resources can be controlled.

### 6. Controlled Key Access

Encryption keys or other playback credentials can be exposed only after the required authorization checks.

### 7. Dedicated Player

Playback is performed through the application's controlled player rather than exposing the original source directly.

---

# Important Security Note

Encryption by itself is not equivalent to a complete commercial DRM system.

For example, HLS AES encryption can protect media segments, but a production DRM architecture normally adds a secure license/key service and platform DRM technologies such as Widevine, PlayReady or FairPlay where required. The underlying PHP FFmpeg streaming library itself also distinguishes HLS encryption from a full DRM solution.

Therefore, the security model of this project should be evaluated as a **layered protected-streaming architecture**, with the actual DRM strength depending on the implementation of key management, authorization, player integration and client environment.

---

# Project Structure

```text
streaming/
│
├── app/
│   └── Core application
│
├── Modules/
│   └── Domain modules
│
├── bootstrap/
│
├── config/
│
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
│
├── public/
│
├── resources/
│
├── routes/
│
├── storage/
│
├── tests/
│
├── artisan
├── composer.json
├── package.json
└── vite.config.js
```

---

# Development

Run the test suite:

```bash
php artisan test
```

Run Laravel Pint:

```bash
./vendor/bin/pint
```

Clear application caches:

```bash
php artisan optimize:clear
```

---

# Architecture Summary

```text
                         ┌───────────────────┐
                         │      Client       │
                         └─────────┬─────────┘
                                   │
                                   ▼
                         ┌───────────────────┐
                         │   Custom Player   │
                         └─────────┬─────────┘
                                   │
                                   ▼
                         ┌───────────────────┐
                         │   Auth / Access   │
                         │      Control      │
                         └─────────┬─────────┘
                                   │
                                   ▼
                         ┌───────────────────┐
                         │ Streaming API     │
                         └─────────┬─────────┘
                                   │
                    ┌──────────────┴──────────────┐
                    │                             │
                    ▼                             ▼
             Protected Media               Key / Access
                    │                       Management
                    ▼                             │
               S3 / Storage                      │
                    │                             │
                    └──────────────┬──────────────┘
                                   ▼
                              Playback
```

The backend is responsible for controlling access and preparing protected media, while FFmpeg handles the media-processing pipeline and the dedicated player handles playback.

---

# Project Goals

The project aims to provide a foundation for building secure video platforms where the content owner needs more control over how video is processed, stored, delivered and consumed.

Primary goals:

* Protect original video assets
* Encrypt streaming media
* Control playback authorization
* Separate media processing from application logic
* Support scalable media storage
* Provide a dedicated playback experience
* Build a modular foundation for future DRM capabilities

---

## License

This project is licensed under the MIT License.
