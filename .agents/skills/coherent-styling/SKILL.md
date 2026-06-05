---
name: coherent-styling
description: "Activate when styling the Aquafin application. Use for any CSS updates, Blade template styling, component styling, color system usage, or visual design work. Ensures all new styles follow the unified color system and component patterns established in the codebase. Covers consistent button styling, form inputs, tables, alerts, navigation, and color palette usage."
license: MIT
metadata:
  author: aquafin
---

# Coherent Styling for Aquafin

## Unified Color System

All colors are defined as CSS variables in `public/css/style.css` at the `:root` level. Always use these variables instead of hardcoded hex values.

### Primary Colors
```css
--primary: #2563eb           /* Main action color, nav background, primary buttons */
--primary-dark: #1d4ed8      /* Hover/active states for primary actions */
--primary-light: #3b82f6     /* Light variant for backgrounds */
```

### Secondary Colors
```css
--secondary: #0891b2         /* Submit buttons, secondary CTAs */
--secondary-dark: #0e7490    /* Hover state for secondary buttons */
```

### Semantic Colors
```css
--success: #16a34a           /* Success messages, confirmations */
--success-light: #f0fdf4     /* Success backgrounds */
--danger: #dc2626            /* Errors, warnings, delete actions */
--danger-light: #fee2e2      /* Error backgrounds */
--danger-dark: #991b1b       /* Dark error for text */
--warning: #d97706           /* Warning messages, test indicators */
--warning-light: #fef08a     /* Warning backgrounds */
```

### Text & Neutral
```css
--text-dark: #1f2937         /* Primary text, headings */
--text-medium: #4b5563       /* Secondary text */
--text-light: #6b7280        /* Tertiary text, subtitles */
--text-lighter: #9ca3af      /* Disabled text, hints */
--border: #e5e7eb            /* All borders */
--bg-light: #f9fafb          /* Light backgrounds, table headers */
--bg-white: #ffffff          /* White backgrounds */
--slate-800: #1e293b         /* Dark backgrounds (weather cards) */
--slate-700: #334155         /* Borders on dark backgrounds */
--slate-400: #94a3b8         /* Light text on dark backgrounds */
```

## CSS Organization

`public/css/style.css` is organized into 18 sections:

1. **COLOR SYSTEM** - CSS variables (use these!)
2. **NAVIGATION** - Nav bar, links, branding
3. **BUTTONS** - All button variants (.btn-primary, .btn-action, etc.)
4. **CONTENT & LAYOUT** - Container, grid, spacing
5. **LOGIN PAGE** - Login-specific styles
6. **FORMS** - Inputs, labels, error states
7. **HERO & PAGE TITLES** - Hero boxes, titles, subtitles
8. **TABLES** - Table, th, td styling
9. **ALERTS & BADGES** - Alert boxes, badge variants
10. **CATEGORY & MATERIALS** - Category blocks, material lists
11. **SEARCH & FILTER** - Search inputs, buttons, filters
12. **USER MANAGEMENT** - User forms, action buttons
13. **WEATHER & FORECAST CARDS** - Dark weather card, stats
14. **STATS & FORECAST** - Stats grid, forecast items
15. **WEATHER PAGE LAYOUT** - Weather page specific layout
16. **MANAGEMENT PANEL** - Simulation panel, stock lists
17. **STOCK LIST** - Stock item rows, checkboxes
18. **UTILITY CLASSES** - Table centering, empty states

## Consistency First

Before styling anything new:
1. Check sibling files/components for established patterns
2. Use existing classes before creating new ones
3. Follow the naming convention: `.component-element` or `.component-element--modifier`
4. Ensure hover/focus states match the system

## Component Styling Patterns

### Buttons

**Primary Action Button**
```html
<button class="btn-primary">Save</button>
```
✅ Use for main actions (save, submit, create)

**Action Button (Edit/Delete)**
```html
<a href="#" class="btn-action btn-action-edit">Edit</a>
<button class="btn-action btn-action-delete">Delete</button>
```
✅ Use for row actions in tables

**Secondary Button**
```html
<button class="submit-btn">Submit</button>
```
✅ Use for secondary CTAs (teal color)

**Add/Create Button**
```html
<a href="#" class="btn-add-user">Add User</a>
```
✅ Use for "add new" actions

### Forms

```html
<div class="form-group">
    <label for="input" class="form-label">Label Text</label>
    <input type="text" id="input" class="form-input" />
</div>
```

✅ Always use `.form-group` for spacing
✅ Always use `.form-label` for labels
✅ Always use `.form-input` for inputs/selects
✅ Focus state is automatic with box-shadow

### Tables

```html
<table class="custom-table">
    <thead>
        <tr>
            <th>Column 1</th>
            <th>Column 2</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="table-center">Centered</td>
            <td class="table-right">Right-aligned</td>
        </tr>
    </tbody>
</table>
```

✅ Use `.custom-table` for main tables
✅ Use `.users-table` for user management tables
✅ Use `.table-center` for center-aligned cells
✅ Use `.table-right` for right-aligned cells

### Alerts & Badges

**Alerts**
```html
<div class="weather-alert weather-alert--danger">Error message</div>
<div class="weather-alert weather-alert--ok">Success message</div>
```

✅ Danger: Red background with red border
✅ OK: Green background with green border
✅ Automatically uses proper text color

**Badges**
```html
<span class="badge badge-important">Important</span>
<span class="badge badge-normal">Normal</span>
<span class="badge rain">Regen</span>
<span class="badge dry">Droog</span>
```

✅ `.badge-important` - Red background (for critical items)
✅ `.badge-normal` - Green background (for normal items)
✅ `.rain` / `.dry` - Weather-specific badges

### Empty States

```html
<div class="empty-state">
    <p class="empty-state-title">No data found</p>
    <p class="empty-state-subtitle">Try adjusting your filters.</p>
</div>
```

## No Inline Styles

**❌ WRONG:**
```html
<div style="text-align: center; padding: 40px; color: #6b7280;">
    <p style="font-size: 16px;">Content</p>
</div>
```

**✅ RIGHT:**
```html
<div class="empty-state">
    <p class="empty-state-title">Content</p>
</div>
```

**❌ WRONG:**
```html
<button style="background: #dc2626; color: white; padding: 10px;">Delete</button>
```

**✅ RIGHT:**
```html
<button class="btn-action btn-action-delete">Delete</button>
```

## Adding New Styles

When creating new CSS:

1. **Place in correct section** of `public/css/style.css`
2. **Use CSS variables** for all colors
3. **Include hover/focus states** for interactive elements
4. **Use rem units** for consistent spacing (not pixels)
5. **Comment section headers** with `/* ==================== NAME ==================== */`

### Example - New Component

```css
/* ==================== NEW COMPONENT ==================== */
.my-component {
    background: var(--bg-white);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 1rem;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
}

.my-component:hover {
    background: var(--bg-light);
}

.my-component--active {
    border-color: var(--primary);
    background: rgba(37, 99, 235, 0.05);
}
```

## Navigation Styling

Navigation is in the **NAVIGATION** section. Key points:

✅ `.nav-link` has white text (not black)
✅ `.nav-link:hover` has background color change
✅ `.nav-brand` is the logo
✅ `.nav-user` is user display name

```html
<a href="#" class="nav-link">Link Text</a>
```

## Weather Cards

Weather cards use dark slate background:

✅ Background: `var(--slate-800)` (#1e293b)
✅ Borders: `var(--slate-700)` (#334155)
✅ Text on dark: `var(--slate-400)` (#94a3b8)
✅ Stat values: Blue (`#60a5fa`) or Cyan (`#22d3ee`)

```html
<div class="weather-card">
    <h1 class="card-title weather-card-title">Title</h1>
    <div class="stats-grid">
        <div class="stat-box">
            <span class="stat-label">Label</span>
            <span class="stat-value value-current">28 <span class="unit">mm</span></span>
        </div>
    </div>
</div>
```

## Testing Styling Changes

1. After CSS changes, build with `npm run build`
2. Check browser DevTools for visual rendering
3. Test all button hover states
4. Test form focus states
5. Test on light and dark backgrounds
6. Verify color contrast (WCAG AA minimum)

## Common Mistakes to Avoid

❌ **Hardcoding colors** - Use CSS variables instead
❌ **Inline `style=""` attributes** - Use CSS classes
❌ **Inconsistent hover states** - All interactive elements need hover
❌ **Wrong border color** - Use `var(--border)` consistently
❌ **Missing `.form-input` class** - All inputs must use it
❌ **Pixel units for spacing** - Use rem for scalability
❌ **New button variant** - Check existing `.btn-*` classes first

## Quick Reference - Common Classes

| Class | Use | Color |
|-------|-----|-------|
| `.btn-primary` | Main action button | Blue (#2563eb) |
| `.btn-action-edit` | Edit row action | Blue (#2563eb) |
| `.btn-action-delete` | Delete row action | Red (#dc2626) |
| `.submit-btn` | Form submit | Teal (#0891b2) |
| `.btn-add-user` | Add new item | Blue (#2563eb) |
| `.form-input` | Form inputs/selects | Gray borders |
| `.custom-table` | Main data table | White bg |
| `.weather-card` | Dark info card | Dark slate |
| `.badge-important` | Critical badge | Red |
| `.badge-normal` | Normal badge | Green |
| `.weather-alert--danger` | Error alert | Red bg |
| `.weather-alert--ok` | Success alert | Green bg |
| `.empty-state` | No data message | Gray text |

## Verification Scripts

When updating styles:
- Run `npm run build` to compile CSS
- No verification script needed - tests are visual
- Check browser DevTools for generated CSS
- Ensure CSS variables are compiled correctly

---

**Last Updated**: June 5, 2026
**Coherent Styling System**: Fully implemented with unified color palette and component patterns
