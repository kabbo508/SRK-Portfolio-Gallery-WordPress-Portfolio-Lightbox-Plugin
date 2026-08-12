# SRK Portfolio Gallery

A WordPress portfolio plugin based on the supplied reference design.

## Features

- Filterable portfolio grid.
- Portfolio custom post type.
- Portfolio Categories taxonomy.
- Commercial, Residential, and Industrial categories created on activation.
- Featured image on each portfolio card.
- Hover dark overlay with:
  - Zoom/lightbox button.
  - Link to single portfolio page.
- Multiple-image gallery uploader in the backend.
- Drag-and-drop gallery ordering.
- Global single-page banner under Portfolio → Settings.
- Optional banner override per portfolio item.
- Single portfolio page with title and Back to portfolio breadcrumb.
- 3-column gallery desktop, 2-column tablet, 1-column mobile.
- Gallery hover overlay.
- Full-screen lightbox with next/previous buttons.
- Keyboard navigation: Left, Right, Escape.
- Adjustable accent color, banner height, banner overlay opacity, grid columns, and grid gap.
- CSS files load without a CSS version query string.

## Installation

1. Upload the plugin ZIP in WordPress → Plugins → Add New → Upload Plugin.
2. Activate **SRK Portfolio Gallery**.
3. Go to Portfolio → Settings.
4. Choose the global single-page banner.
5. Create a WordPress page named Portfolio.
6. Add this shortcode:

   `[srk_portfolio]`

7. Return to Portfolio → Settings and select that page as the Portfolio Page.
8. Add Portfolio items.
9. Set a Featured Image for each item.
10. Assign a Portfolio Category.
11. Add multiple images in the Portfolio Gallery box.
12. Drag images into the order you want.
13. Publish.

## Shortcode

Basic:

`[srk_portfolio]`

Optional:

`[srk_portfolio columns="3"]`

`[srk_portfolio columns="4" gap="24"]`

`[srk_portfolio columns="3" limit="12"]`

Supported column values: 2, 3, 4.

## CSS files

- `assets/css/frontend.css`
- `assets/css/admin.css`

No CSS version is appended by WordPress. Browser/CDN cache may still need clearing after CSS edits.


## Category filter visibility and exact order

Go to **Portfolio → Settings → Frontend Category Filters**.

There are two drag-and-drop lists:

- **Available Filters** — hidden on the frontend.
- **Frontend Filters — Drag to Set Exact Order** — visible on the frontend.

Drag filters between the two lists to show/hide them. Drag items up/down in the Frontend Filters list to control the exact frontend order.

**ALL** is also draggable and can be hidden. If ALL is hidden, the first selected category becomes the default active filter.

Example:

    Residential
    Commercial
    All
    Industrial

Frontend order:

    RESIDENTIAL / COMMERCIAL / ALL / INDUSTRIAL


## Featured image fallback

Portfolio grid cards use this image priority:

1. Portfolio **Featured Image**
2. If no Featured Image exists, the **first image in the Portfolio Gallery**
3. If neither exists, the normal placeholder

The fallback uses the gallery's saved drag-and-drop order, so changing which gallery image is first also changes the grid image when no Featured Image is assigned.


## v1.1.2 single-page layout update

- Forces the SRK Portfolio single-page content area to a solid white background so theme texture/background images do not show through.
- Uses a light image banner treatment with the Portfolio title rendered as the page H1.
- Styles the breadcrumb inside a white box underneath the H1.
- Gallery heading now displays only **Image Gallery** instead of appending the Portfolio title.
