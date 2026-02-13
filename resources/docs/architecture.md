# System Architecture

This document describes the high-level architecture of Nexus.

## Overview

Nexus is built on the TALL stack (Tailwind, Alpine, Laravel, Livewire).

## Core Components

### Authentication

We use Laravel Fortify for backend authentication logic.

- Custom Login/Register views
- 2FA via Email or Authenticator App
- Social Login (planned)

### Database Schema

The database is PostgreSQL.

- `users`: Core user data
- `sessions`: Session management
- `personal_access_tokens`: API tokens

## Frontend Architecture

The frontend is built with Blade and Livewire.

### Flux UI

We use Flux for our component library.

- Buttons
- Inputs
- Modals
- Dropdowns

### Styling

Tailwind CSS v4 is used for all styling.

- Custom color palette (Stone, Amber/Orange)
- Dark mode support
- Typography plugin for docs

## Deployment

Nexus is Dockerized.

- `app`: Laravel container
- `pgsql`: Database container
- `redis`: Cache/Queue container

### Production

We deploy using Coolify.
