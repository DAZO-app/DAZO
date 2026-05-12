/**
 * Flattens a list of circles (with children) into a hierarchical array
 * suitable for <select> options with visual indentation.
 *
 * @param {Array} circles - Circles with optional `active_children` or `children` property
 * @param {Object} options - { excludeArchived: true }
 * @returns {Array} - [{ id, name, displayName, depth, isChild, isArchived }]
 */
export function flattenCirclesWithHierarchy(circles, options = {}) {
    const { excludeArchived = true } = options;
    const result = [];

    if (!Array.isArray(circles)) return result;

    for (const circle of circles) {
        // Add parent circle
        result.push({
            id: circle.id,
            name: circle.name,
            displayName: circle.name,
            depth: 0,
            isChild: false,
            isArchived: !!circle.archived_at || !!circle.is_archived,
        });

        // Add children (prefer active_children, fall back to children)
        const children = circle.active_children || circle.children || [];
        for (const child of children) {
            const isArchived = !!child.archived_at || !!child.is_archived;
            if (excludeArchived && isArchived) continue;

            result.push({
                id: child.id,
                name: child.name,
                displayName: `\u00A0\u00A0↳ ${child.name}`,
                depth: 1,
                isChild: true,
                isArchived,
            });
        }
    }

    return result;
}

/**
 * Translates circle type to French label
 */
export function circleTypeLabel(type) {
    const map = {
        open: 'Ouvert',
        closed: 'Fermé',
        observer_open: 'Observateurs',
    };
    return map[type] || type || 'Standard';
}

/**
 * Returns badge CSS class for circle type
 */
export function circleTypeBadge(type) {
    const map = {
        open: 'badge-teal',
        closed: 'badge-red',
        observer_open: 'badge-blue',
    };
    return map[type] || 'badge-gray';
}
