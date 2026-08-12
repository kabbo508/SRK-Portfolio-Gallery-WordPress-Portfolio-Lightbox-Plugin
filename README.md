# SRK Portfolio Gallery

**SRK Portfolio Gallery** is a custom WordPress portfolio plugin for creating responsive, filterable project galleries with category controls, hover actions, custom single portfolio pages, admin-managed banners, drag-and-drop image galleries, and lightbox navigation.

**Version:** 1.1.2  
**Author:** srkpics  
**Author Website:** https://sumonrahmankabbo.com/  
**Text Domain:** `srk-portfolio-gallery`

---

## Overview

SRK Portfolio Gallery adds a dedicated Portfolio section to WordPress and provides a complete workflow for managing project categories, portfolio grid items, image galleries, single portfolio pages, and frontend category filters.

The plugin is designed for construction companies, contractors, agencies, studios, architects, electricians, restoration companies, and other businesses that need a visual project portfolio inside WordPress.

The main portfolio grid is added using a shortcode:

```text
[srk_portfolio]
```

Each portfolio item can contain:

- Portfolio title
- Featured image
- Portfolio category
- WordPress editor content
- Multiple gallery images
- Drag-and-drop gallery image ordering
- Optional individual banner override
- Custom single portfolio page

---

## Features

### Portfolio Custom Post Type

The plugin creates a dedicated **Portfolio** post type in the WordPress admin.

Each portfolio item supports:

- Title
- Editor content
- Featured image
- Excerpt
- Page order
- Portfolio categories
- Portfolio gallery
- Banner override

---

### Portfolio Categories

A dedicated hierarchical **Portfolio Categories** taxonomy is included.

On plugin activation, the following starter categories are created automatically:

- Commercial
- Residential
- Industrial

These are normal WordPress taxonomy terms, so they can be edited, deleted, renamed, or replaced with your own categories.

---

### Admin-Controlled Frontend Filters

Go to:

**Portfolio → Settings → Frontend Category Filters**

The plugin provides two drag-and-drop areas:

#### Available Filters

Categories placed here are hidden from the frontend filter bar.

#### Frontend Filters — Drag to Set Exact Order

Only categories placed here are displayed on the frontend.

The exact drag-and-drop order saved in the admin is used on the frontend.

For example, if the saved order is:

```text
Residential
Commercial
All
Industrial
```

the frontend filter order will be:

```text
RESIDENTIAL / COMMERCIAL / ALL / INDUSTRIAL
```

The special **ALL** filter can also be:

- Reordered
- Moved anywhere in the filter list
- Hidden completely

If **ALL** is hidden, the first selected category becomes the default active filter.

---

## Responsive Portfolio Grid

The frontend portfolio grid supports configurable columns.

Supported shortcode column values:

- 2 columns
- 3 columns
- 4 columns

Example:

```text
[srk_portfolio columns="3"]
```

Custom grid gap:

```text
[srk_portfolio columns="4" gap="24"]
```

Limit the number of portfolio items:

```text
[srk_portfolio columns="3" limit="12"]
```

The default grid settings can also be controlled from:

**Portfolio → Settings**

---

## Portfolio Card Hover Actions

Each portfolio card displays an image with a hover overlay.

On hover, two circular action buttons appear:

### Zoom Button

Opens the portfolio image in a lightbox.

### Link Button

Opens the single portfolio page.

The overlay and actions are designed to keep the portfolio grid visual and easy to navigate.

---

## Featured Image Fallback

The portfolio grid uses the following image priority:

1. Portfolio **Featured Image**
2. If no Featured Image exists, the **first image from the Portfolio Gallery**
3. If neither exists, the plugin displays its placeholder

Because gallery images are drag-and-drop sortable, changing the first gallery image also changes the portfolio grid image when no Featured Image has been assigned.

---

## Multiple Image Gallery

Each Portfolio item contains a **Portfolio Gallery** meta box.

From the backend you can:

- Select multiple images from the WordPress Media Library
- Add additional images
- Remove individual gallery images
- Drag images into the exact order you want

The saved order is used on the single portfolio page and in the Featured Image fallback system.

---

## Single Portfolio Page

The plugin includes its own single portfolio template.

Each single portfolio page contains:

- Custom banner area
- Portfolio title displayed as an `H1`
- Back to Portfolio breadcrumb
- Optional portfolio editor content
- `Image Gallery` heading
- Responsive image gallery
- Image hover overlay
- Full-screen lightbox

The portfolio content area uses a solid white background to prevent the active WordPress theme's body texture or background image from showing through the portfolio page.

---

## Single Page Banner

A global banner image can be configured from:

**Portfolio → Settings**

Available banner options include:

- Global banner image
- Banner height
- Banner overlay opacity
- Accent color

Each individual Portfolio item also contains a **Banner Override** option.

If an individual banner is selected, it is used for that portfolio item.

If no individual banner is selected, the global Portfolio banner is used.

---

## Image Gallery Layout

The single portfolio gallery is responsive.

Default layout:

- Desktop: 3 columns
- Tablet: 2 columns
- Mobile: 1 column

Each image has a hover overlay with a zoom action.

---

## Lightbox

Clicking an image opens a full-screen lightbox.

Supported controls include:

- Previous image
- Next image
- Close button
- Keyboard Left Arrow
- Keyboard Right Arrow
- Escape key to close

The lightbox allows visitors to navigate all images belonging to the current portfolio item.

---

## Installation

1. Download the plugin ZIP.
2. Log in to WordPress.
3. Go to **Plugins → Add New Plugin**.
4. Click **Upload Plugin**.
5. Upload the SRK Portfolio Gallery ZIP file.
6. Activate **SRK Portfolio Gallery**.

After activation, a new **Portfolio** menu will appear in the WordPress admin.

---

## Initial Setup

### Step 1 — Configure Portfolio Settings

Go to:

**Portfolio → Settings**

Configure:

- Global banner
- Banner height
- Banner overlay opacity
- Accent color
- Default grid columns
- Grid gap
- Frontend category filters
- Portfolio page

---

### Step 2 — Create the Main Portfolio Page

Create a normal WordPress page, for example:

```text
Portfolio
```

Add the shortcode:

```text
[srk_portfolio]
```

Publish the page.

Then return to:

**Portfolio → Settings**

and select that page as the **Portfolio Page**.

This page is used by the **Back to portfolio** link on single portfolio pages.

---

### Step 3 — Create Portfolio Categories

Go to:

**Portfolio → Categories**

Create or manage the categories you need.

Examples:

- Commercial
- Residential
- Industrial
- Retail
- Restaurants
- Offices
- Multi-Family
- Restoration
- Electrical
- Construction

Then go to **Portfolio → Settings** and drag only the categories you want into the **Frontend Filters** list.

---

### Step 4 — Create a Portfolio Item

Go to:

**Portfolio → Add New**

Add:

1. Portfolio title
2. Optional content
3. Portfolio category
4. Featured image, if desired
5. Gallery images
6. Optional banner override

Drag gallery images into the correct order and publish the Portfolio item.

---

## Shortcode Reference

### Basic Portfolio

```text
[srk_portfolio]
```

### Three Columns

```text
[srk_portfolio columns="3"]
```

### Four Columns

```text
[srk_portfolio columns="4"]
```

### Custom Grid Gap

```text
[srk_portfolio columns="3" gap="30"]
```

### Limit Portfolio Items

```text
[srk_portfolio columns="3" limit="9"]
```

### Combined Example

```text
[srk_portfolio columns="4" gap="24" limit="12"]
```

---

## Admin Settings

The Portfolio settings screen provides controls for:

| Setting | Purpose |
|---|---|
| Global Single Page Banner | Default banner used on Portfolio single pages |
| Banner Height | Controls the single-page hero height |
| Banner Overlay Opacity | Controls the light banner overlay |
| Accent Color | Controls plugin accent elements |
| Frontend Category Filters | Show, hide, and reorder filter categories |
| Default Grid Columns | Sets default Portfolio grid columns |
| Grid Gap | Controls spacing between grid items |
| Portfolio Page | Defines the page used for Back to Portfolio navigation |

---

## Category Filter Ordering

Frontend filter order is not based on alphabetical taxonomy order.

The plugin stores the manually selected filter order from the Portfolio settings page.

This means the frontend always follows the admin-defined order.

Categories can be moved between:

```text
Available Filters
```

and:

```text
Frontend Filters
```

Only filters inside **Frontend Filters** are rendered on the frontend.

---

## CSS Files

Frontend CSS:

```text
assets/css/frontend.css
```

Admin CSS:

```text
assets/css/admin.css
```

The plugin intentionally loads these CSS files without a WordPress CSS version query string.

This makes direct stylesheet updates easier.

Depending on your hosting environment, browser cache, server cache, CDN, or WordPress caching plugins may still need to be cleared after CSS changes.

---

## JavaScript Files

Frontend functionality:

```text
assets/js/frontend.js
```

Admin functionality:

```text
assets/js/admin.js
```

Frontend JavaScript handles:

- Portfolio filtering
- Initial active filter
- Lightbox opening
- Previous/next image navigation
- Keyboard navigation
- Lightbox closing

Admin JavaScript handles:

- WordPress Media Library selection
- Multiple gallery image selection
- Gallery drag-and-drop ordering
- Banner image selection
- Frontend category drag-and-drop management

---

## Plugin Structure

```text
srk-portfolio-gallery/
├── srk-portfolio-gallery.php
├── README.md
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── frontend.css
│   └── js/
│       ├── admin.js
│       └── frontend.js
└── templates/
    └── single-tpg_portfolio.php
```

---

## Version 1.1.2

Current functionality includes:

- Portfolio custom post type
- Portfolio category taxonomy
- Filterable portfolio grid
- Admin-selectable frontend categories
- Drag-and-drop filter ordering
- Movable/hideable ALL filter
- Multiple image galleries
- Drag-and-drop gallery ordering
- Featured Image fallback to first gallery image
- Portfolio hover actions
- Image lightbox
- Previous/next navigation
- Keyboard navigation
- Global portfolio banner
- Per-item banner override
- Portfolio title as single-page `H1`
- White single-page content background
- Simplified `Image Gallery` heading
- Responsive portfolio and gallery layouts

---

## Author

**srkpics**  
https://sumonrahmankabbo.com/

---

## Notes

This plugin uses standard WordPress functionality including:

- Custom Post Types
- Custom Taxonomies
- WordPress Media Library
- Featured Images
- Post Meta
- WordPress Settings API
- Shortcodes

No separate custom database tables are required.
