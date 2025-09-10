export default {
    mounted(el, binding) {
        const tooltipText = binding.value || '';
        const position = Object.keys(binding.modifiers)[0] || 'top';

        const tooltip = document.createElement('div');
        tooltip.className = `tooltip tooltip-${position}`;
        tooltip.textContent = tooltipText;
        document.body.appendChild(tooltip);

        const positionTooltip = () => {
            const rect = el.getBoundingClientRect();
            const tooltipRect = tooltip.getBoundingClientRect();

            let top, left;
            switch (position) {
                case 'bottom':
                    top = rect.bottom + 4;
                    left = rect.left + rect.width / 2 - tooltipRect.width / 2;
                    break;
                case 'left':
                    top = rect.top + rect.height / 2 - tooltipRect.height / 2;
                    left = rect.left - tooltipRect.width - 8;
                    break;
                case 'right':
                    top = rect.top + rect.height / 2 - tooltipRect.height / 2;
                    left = rect.right + 8;
                    break;
                default:
                    top = rect.top - tooltipRect.height - 4;
                    left = rect.left + rect.width / 2 - tooltipRect.width / 2;
            }

            tooltip.style.top = `${top + window.scrollY}px`;
            tooltip.style.left = `${left + window.scrollX}px`;
        };

        const showTooltip = () => {
            tooltip.style.opacity = '1';
            tooltip.style.visibility = 'visible';
            positionTooltip();
            window.addEventListener('scroll', hideTooltip);
        };

        const hideTooltip = () => {
            tooltip.style.opacity = '0';
            tooltip.style.visibility = 'hidden';
            window.removeEventListener('scroll', hideTooltip);
        };

        el._tooltip = tooltip;
        el._showTooltip = showTooltip;
        el._hideTooltip = hideTooltip;
        el.addEventListener('mouseenter', showTooltip);
        el.addEventListener('mouseleave', hideTooltip);
    },

    beforeUnmount(el) {
        if (el._tooltip) {
            el._tooltip.remove();
            delete el._tooltip;
        }
        if (el._showTooltip) {
            el.removeEventListener('mouseenter', el._showTooltip);
            delete el._showTooltip;
        }
        if (el._hideTooltip) {
            el.removeEventListener('mouseleave', el._hideTooltip);
            delete el._hideTooltip;
        }
    },
};
