(function() {
    const API_BASE_URL = window.location.origin + '/api/v1/public';
    
    const PALETTES = {
        'default': ['#3b82f6', '#f8fafc', '#1e293b', '#e2e8f0'],
        'red': ['#e11d48', '#fff1f2', '#881337', '#fecdd3'],
        'yellow': ['#eab308', '#fefce8', '#713f12', '#fef08a'],
        'green': ['#16a34a', '#f0fdf4', '#14532d', '#bbf7d0'],
        'purple': ['#9333ea', '#faf5ff', '#581c87', '#e9d5ff'],
        'orange': ['#f97316', '#fff7ed', '#7c2d12', '#fed7aa'],
        'teal': ['#0d9488', '#f0fdfa', '#134e4a', '#99f6e4'],
        'rose': ['#f43f5e', '#fff1f2', '#881337', '#fecdd3'],
        'indigo': ['#4f46e5', '#eef2ff', '#312e81', '#c7d2fe'],
        'gray': ['#4b5563', '#f9fafb', '#111827', '#e5e7eb'],
        'dark': ['#1f2937', '#111827', '#f3f4f6', '#374151'],
        'midnight': ['#1e3a8a', '#0f172a', '#e2e8f0', '#1e293b'],
        'forest_dark': ['#064e3b', '#022c22', '#d1fae5', '#065f46'],
        'high_contrast': ['#000000', '#ffffff', '#000000', '#000000']
    };

    const STATUS_LABELS = {
        'draft': 'Brouillon',
        'clarification': 'Clarification',
        'reaction': 'Réaction',
        'objection': 'Objection',
        'revision': 'En Révision',
        'adopted': 'Adoptée',
        'suspended': 'Suspendue',
        'abandoned': 'Abandonnée',
        'rejected': 'Rejetée'
    };

    function injectStyles() {
        if (document.getElementById('dazo-widget-styles')) return;
        const style = document.createElement('style');
        style.id = 'dazo-widget-styles';
        style.innerHTML = `
            .dazo-widget { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; line-height: 1.5; }
            .dazo-card { border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); margin-bottom: 20px; transition: transform 0.2s; }
            .dazo-card:hover { transform: translateY(-2px); }
            .dazo-header { padding: 16px; border-bottom: 1px solid rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: space-between; }
            .dazo-body { padding: 16px; }
            .dazo-footer { padding: 12px 16px; border-top: 1px solid rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
            .dazo-title { margin: 0 0 12px 0; font-size: 1.1rem; font-weight: 700; color: inherit; text-decoration: none; display: block; }
            .dazo-meta { display: flex; flex-wrap: wrap; gap: 8px; font-size: 0.8rem; color: #64748b; margin-bottom: 12px; }
            .dazo-badge { padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; }
            .dazo-author-line { font-size: 0.85rem; margin-bottom: 8px; }
            .dazo-detail-section { margin-top: 16px; padding-top: 12px; border-top: 1px dashed rgba(0,0,0,0.1); }
            .dazo-detail-item { font-size: 0.8rem; display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
            .dazo-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
            .dazo-link { font-size: 0.75rem; font-weight: 700; text-decoration: none; text-transform: uppercase; letter-spacing: 0.5px; }
            .dazo-list-item { display: flex; gap: 12px; padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.05); }
            .dazo-list-item:last-child { border-bottom: none; }
            .dazo-list-content { flex: 1; }
            .dazo-list-title { font-weight: 600; font-size: 0.95rem; display: block; margin-bottom: 4px; color: inherit; text-decoration: none; }
            .dazo-loading { padding: 20px; text-align: center; color: #64748b; font-size: 0.9rem; }
            .dazo-error { padding: 20px; text-align: center; color: #ef4444; background: #fef2f2; border-radius: 8px; font-size: 0.9rem; }
        `;
        document.head.appendChild(style);
    }

    async function fetchData(url, apiKey) {
        try {
            const response = await fetch(url, {
                headers: { 'X-API-Key': apiKey, 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Erreur réseau');
            return await response.json();
        } catch (e) {
            console.error('DAZO Widget Error:', e);
            return null;
        }
    }

    function renderSingle(el, data, config, colors) {
        const decision = data.decision || data.data; // Handle Resource wrapper
        if (!decision) return;

        const isDark = ['dark', 'midnight', 'forest_dark'].includes(config.theme);
        const textColor = isDark ? colors[2] : '#1e293b';
        const mutedColor = isDark ? 'rgba(255,255,255,0.6)' : '#64748b';

        let detailsHtml = '';
        if (config.show_detail && decision.participation_stats) {
            const stats = decision.participation_stats;
            if (config.show_clarifications) detailsHtml += `<div class="dazo-detail-item"><div class="dazo-dot" style="background:${colors[0]}"></div><span>${stats.clarifications_count || 0} Clarifications</span></div>`;
            if (config.show_reactions) detailsHtml += `<div class="dazo-detail-item"><div class="dazo-dot" style="background:${colors[0]};opacity:0.7"></div><span>${stats.reactions_count || 0} Réactions</span></div>`;
            if (config.show_objections) detailsHtml += `<div class="dazo-detail-item"><div class="dazo-dot" style="background:${colors[0]};opacity:0.5"></div><span>${stats.objections_count || 0} Objections</span></div>`;
            if (config.show_suggestions) detailsHtml += `<div class="dazo-detail-item"><div class="dazo-dot" style="background:${colors[0]};opacity:0.3"></div><span>Suggestions disponibles</span></div>`;
        }

        const authorName = decision.author?.user?.name || 'N/A';

        el.innerHTML = `
            <div class="dazo-card" style="background:${colors[1]}; color:${textColor}; border-color:${colors[3]}">
                <div class="dazo-header">
                    <span class="dazo-badge" style="background:${colors[3]}; color:${colors[0]}">${STATUS_LABELS[decision.status] || decision.status}</span>
                    <span style="font-size: 10px; opacity: 0.5;">v${decision.current_version?.version_number || 1}</span>
                </div>
                <div class="dazo-body">
                    <a href="${window.location.origin}/front/decisions/${decision.id}" class="dazo-title" style="color:${textColor}">${decision.title}</a>
                    <div class="dazo-author-line" style="color:${mutedColor}">Proposé par <strong>${authorName}</strong></div>
                    ${detailsHtml ? `<div class="dazo-detail-section" style="border-top-color:${colors[3]}">${detailsHtml}</div>` : ''}
                </div>
                <div class="dazo-footer" style="background:rgba(0,0,0,0.02)">
                    <span style="font-size: 0.7rem; color:${mutedColor}">${new Date(decision.created_at).toLocaleDateString()}</span>
                    <a href="${window.location.origin}/front/decisions/${decision.id}" class="dazo-link" style="color:${colors[0]}">Voir sur DAZO</a>
                </div>
            </div>
        `;
    }

    function renderList(el, data, config, colors) {
        const decisions = data.data || [];
        if (decisions.length === 0) {
            el.innerHTML = '<div class="dazo-loading">Aucune décision à afficher.</div>';
            return;
        }

        const isDark = ['dark', 'midnight', 'forest_dark'].includes(config.theme);
        const textColor = isDark ? colors[2] : '#1e293b';
        const mutedColor = isDark ? 'rgba(255,255,255,0.6)' : '#64748b';

        let listHtml = '';
        decisions.forEach(decision => {
            let detailLine = '';
            if (config.show_detail && decision.participation_stats) {
                const stats = decision.participation_stats;
                detailLine = `<div style="display:flex;gap:8px;margin-top:4px;font-size:0.7rem;opacity:0.8;">
                    ${config.show_clarifications ? `<span>${stats.clarifications_count || 0} cl.</span>` : ''}
                    ${config.show_reactions ? `<span>${stats.reactions_count || 0} réac.</span>` : ''}
                </div>`;
            }

            listHtml += `
                <div class="dazo-list-item" style="border-bottom-color:${colors[3]}">
                    <div class="dazo-dot" style="background:${colors[0]}; margin-top: 6px;"></div>
                    <div class="dazo-list-content">
                        <a href="${window.location.origin}/front/decisions/${decision.id}" class="dazo-list-title" style="color:${textColor}">${decision.title}</a>
                        <div style="font-size:0.75rem; color:${mutedColor}">
                            ${STATUS_LABELS[decision.status] || decision.status} • ${decision.circle?.name || 'Global'}
                        </div>
                        ${detailLine}
                    </div>
                </div>
            `;
        });

        el.innerHTML = `
            <div class="dazo-card" style="background:${colors[1]}; color:${textColor}; border-color:${colors[3]}">
                <div class="dazo-header">
                    <span style="font-weight:700; font-size:0.9rem;">Dernières décisions</span>
                </div>
                <div class="dazo-list-container">
                    ${listHtml}
                </div>
                <div class="dazo-footer">
                    <a href="${window.location.origin}/front" class="dazo-link" style="color:${colors[0]}; width:100%; text-align:center;">Voir toutes les décisions</a>
                </div>
            </div>
        `;
    }

    async function initWidget(el) {
        const config = {
            type: el.getAttribute('data-type') || 'single',
            theme: el.getAttribute('data-theme') || 'default',
            apiKey: el.getAttribute('data-api-key'),
            id: el.getAttribute('data-id'),
            status: el.getAttribute('data-status'),
            circle_id: el.getAttribute('data-circle-id'),
            category_id: el.getAttribute('data-category-id'),
            show_detail: el.getAttribute('data-show-detail') === 'true',
            show_clarifications: el.getAttribute('data-show-clarifications') === 'true',
            show_reactions: el.getAttribute('data-show-reactions') === 'true',
            show_objections: el.getAttribute('data-show-objections') === 'true',
            show_suggestions: el.getAttribute('data-show-suggestions') === 'true'
        };

        if (!config.apiKey) {
            el.innerHTML = '<div class="dazo-error">Clé API manquante</div>';
            return;
        }

        const colors = PALETTES[config.theme] || PALETTES['default'];
        el.innerHTML = '<div class="dazo-loading">Chargement...</div>';

        let url = `${API_BASE_URL}/decisions`;
        if (config.type === 'single' && config.id) {
            url += `/${config.id}`;
        } else {
            const params = new URLSearchParams();
            if (config.status) params.append('status', config.status);
            if (config.circle_id) params.append('circle', config.circle_id);
            if (config.category_id) params.append('category', config.category_id);
            params.append('per_page', '5');
            url += `?${params.toString()}`;
        }

        const data = await fetchData(url, config.apiKey);
        if (!data) {
            el.innerHTML = '<div class="dazo-error">Impossible de charger les données</div>';
            return;
        }

        if (config.type === 'single') {
            renderSingle(el, data, config, colors);
        } else {
            renderList(el, data, config, colors);
        }
    }

    function initAll() {
        injectStyles();
        document.querySelectorAll('.dazo-widget').forEach(initWidget);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();
