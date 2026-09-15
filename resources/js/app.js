document.addEventListener('DOMContentLoaded', () => {
    const notices = document.querySelectorAll('.notice');

    notices.forEach((notice) => {
        setTimeout(() => {
            notice.classList.add('hide');
            setTimeout(() => notice.remove(), 350);
        }, 2500);
    });

    const openTriggers = document.querySelectorAll('.product-edit-trigger, .category-edit-trigger, .open-edit');
    const closeButtons = document.querySelectorAll('.modal-close');

    openTriggers.forEach((trigger) => {
        const openModal = () => {
            const modal = document.getElementById(trigger.dataset.target);
            if (!modal) return;
            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
        };

        trigger.addEventListener('click', (event) => {
            if (event.target.closest('.delete-form')) return;
            openModal();
        });

        trigger.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openModal();
            }
        });
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const modal = button.closest('.product-modal');
            if (!modal) return;
            modal.classList.remove('open');
            modal.setAttribute('aria-hidden', 'true');
        });
    });

    document.querySelectorAll('.product-modal').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.classList.remove('open');
                modal.setAttribute('aria-hidden', 'true');
            }
        });
    });

    document.querySelectorAll('.receipt-card').forEach((card) => {
        const openReceipt = () => {
            window.location.href = card.dataset.receiptUrl;
        };

        card.addEventListener('click', (event) => {
            if (event.target.closest('form, button, a')) return;
            openReceipt();
        });

        card.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openReceipt();
            }
        });
    });
});
