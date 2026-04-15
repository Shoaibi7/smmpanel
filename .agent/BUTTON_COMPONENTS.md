# Reusable Button Components

This document describes the reusable button components created for consistent styling across the application.

## Components Created

### 1. `<x-button>` - Primary Button Component
**Location:** `resources/views/components/button.blade.php`

**Usage:**
```blade
<x-button variant="primary" size="sm" type="button">
    Click Me
</x-button>
```

**Props:**
- `variant` - Button style variant (default: 'primary')
  - `primary` - Orange gradient button (main CTA)
  - `secondary` - Gray button
  - `outline` - Outlined button
  - `danger` - Red button for destructive actions
  - `ghost` - Transparent button

- `size` - Button size (default: 'md')
  - `xs` - Extra small (px-2 py-1, text-[10px])
  - `sm` - Small (px-3 py-1.5, text-xs)
  - `md` - Medium (px-4 py-2, text-xs)
  - `lg` - Large (px-5 py-2.5, text-sm)
  - `xl` - Extra large (px-6 py-3, text-sm)

- `type` - HTML button type (default: 'submit')

**Examples:**
```blade
<!-- Primary action button -->
<x-button variant="primary" size="sm">
    <svg class="w-4 h-4 mr-1.5">...</svg>
    Add Provider
</x-button>

<!-- Danger button -->
<x-button variant="danger" size="md" type="button" onclick="deleteItem()">
    Delete
</x-button>

<!-- Outline button -->
<x-button variant="outline" size="lg">
    Cancel
</x-button>
```

---

### 2. `<x-icon-button>` - Icon-Only Button Component
**Location:** `resources/views/components/icon-button.blade.php`

**Usage:**
```blade
<x-icon-button variant="primary" size="md" title="Edit">
    <svg>...</svg>
</x-icon-button>
```

**Props:**
- `variant` - Button style variant (default: 'secondary')
  - `primary` - Orange icon button
  - `secondary` - Gray icon button
  - `danger` - Red icon button
  - `ghost` - Transparent icon button

- `size` - Button size (default: 'md')
  - `sm` - Small (7x7, icon 3.5x3.5)
  - `md` - Medium (8x8, icon 4x4)
  - `lg` - Large (10x10, icon 5x5)

- `type` - HTML button type (default: 'button')

**Examples:**
```blade
<!-- Edit button -->
<x-icon-button variant="secondary" size="md" title="Edit" onclick="edit()">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
    </svg>
</x-icon-button>

<!-- Delete button -->
<x-icon-button variant="danger" size="md" title="Delete" onclick="deleteItem()">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
    </svg>
</x-icon-button>

<!-- Refresh button -->
<x-icon-button variant="primary" size="sm" title="Refresh" onclick="refresh()">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
    </svg>
</x-icon-button>
```

---

## Design System Features

### Theme Colors
All components use the **orange theme**:
- Primary: Orange gradient (`from-orange-600 to-orange-500`)
- Hover: Darker orange (`from-orange-700 to-orange-600`)
- Focus ring: Orange (`focus:ring-orange-500`)

### Consistent Styling
- **Border radius:** `rounded-xl` for buttons, `rounded-lg` for icon buttons
- **Font weight:** `font-bold` (700)
- **Transitions:** All buttons have smooth transitions
- **Active state:** Scale down effect (`active:scale-95` or `active:scale-90`)
- **Shadows:** Primary buttons have orange-tinted shadows

### Accessibility
- All buttons support `disabled` state
- Icon buttons require `title` attribute for tooltips
- Focus rings for keyboard navigation
- Proper contrast ratios

---

## Migration Guide

### Before (Inline Styles):
```blade
<button type="button" class="bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-600 text-white px-6 py-2.5 rounded-xl text-sm font-black shadow-lg shadow-orange-500/30 transition-all active:scale-95 flex items-center">
    <svg class="w-4 h-4 mr-2">...</svg>
    Add Provider
</button>
```

### After (Component):
```blade
<x-button variant="primary" size="sm">
    <svg class="w-4 h-4 mr-1.5">...</svg>
    Add Provider
</x-button>
```

### Benefits:
✅ **Consistency** - All buttons look the same across the app
✅ **Maintainability** - Update once, apply everywhere
✅ **Smaller code** - Less repetition
✅ **Type safety** - Defined variants prevent mistakes
✅ **Theme updates** - Easy to change colors globally

---

## Usage in API Providers Page

The API Providers page now uses these components:
- **Header "Add Provider" button** - `<x-button variant="primary" size="sm">`
- **Refresh balance icon** - `<x-icon-button variant="primary" size="sm">`
- **Edit icon** - `<x-icon-button variant="secondary" size="md">`
- **Delete icon** - `<x-icon-button variant="danger" size="md">`
- **Empty state button** - `<x-button variant="primary" size="lg">`

---

## Best Practices

1. **Always use components** instead of inline button styles
2. **Choose appropriate size** - sm for compact UIs, md for standard, lg for emphasis
3. **Use icon buttons** for actions in tables/cards
4. **Use regular buttons** for primary CTAs
5. **Add icons** to buttons for better UX (use `mr-1.5` spacing)
6. **Add title attribute** to icon buttons for accessibility
7. **Use uppercase + tracking-widest** for emphasis on important buttons

---

## Future Enhancements

Consider adding:
- Loading state variant
- Icon-left and icon-right props
- Button groups component
- Dropdown button component
- Split button component
