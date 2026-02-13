# Future Feature: Role-Based Access Control (RBAC)

**Date Saved:** 2026-02-13
**Status:** Deferred (Future Roadmap)

## Context

Nexus is an RPG Engine. While initially for single-GM use, future expansion to support multiple players and campaigns will require robust permission management.

## Proposed Roles & Permissions

### 1. Global Roles

- **Admin/Super GM:** Full system access. Can ban users, manage global settings.
- **User:** Standard account. Can create their own campaigns and characters.

### 2. Context-Specific Roles (Per Campaign)

Permissions should be scoped to a specific `Campaign` instance.

- **Gamemaster (GM):**
  - *Permissions:* Edit Campaign, Kick Players, Create/Edit NPCs, See Hidden Rolls, Manage Scenes.
- **Player:**
  - *Permissions:* View Public Scenes, Manage Own Character, Make Rolls, View "Known" NPCs.
- **Spectator:**
  - *Permissions:* View Public Scenes only (Read-only).

### 3. Content Roles (Homebrew Library)

- **Creator:** Can publish Homebrew content (Spells, Items).
- **Reviewer:** Can approve/flag published content.

## Implementation Strategy

- **Package:** `spatie/laravel-permission` is the industry standard.
- **Structure:**
  - `roles` table
  - `permissions` table
  - Model `User` has trait `HasRoles`.
- **Team/Context Support:** Might need `spatie/laravel-permission`'s team feature or a custom Pivot model (`CampaignUser`) with a `role` column for campaign-specific permissions.
