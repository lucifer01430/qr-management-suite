(() => {
  const ready = (cb) => {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', cb, { once: true });
    } else {
      cb();
    }
  };

  const THEME_KEY = 'theme';

  const getStoredTheme = () => localStorage.getItem(THEME_KEY);

  const setStoredTheme = (isDark) => {
    localStorage.setItem(THEME_KEY, isDark ? 'dark' : 'light');
  };

  const applyTheme = (isDark) => {
    document.body.classList.toggle('dark-mode', isDark);
  };

  const updateToggleVisuals = (btn, isDark) => {
    if (!btn) return;
    const icon = btn.querySelector('i');
    const label = btn.querySelector('[data-theme-label]');
    if (icon) {
      icon.classList.toggle('fa-moon', !isDark);
      icon.classList.toggle('fa-sun', isDark);
    }
    if (label) {
      label.textContent = isDark ? 'Light mode' : 'Dark mode';
    }
  };

  const determinePreferredTheme = () => {
    const stored = getStoredTheme();
    if (stored) return stored === 'dark';
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
  };

  const initThemeToggle = () => {
    const toggles = document.querySelectorAll('[data-theme-toggle]');
    if (!toggles.length) return;

    const sync = () => {
      const isDark = document.body.classList.contains('dark-mode');
      toggles.forEach((btn) => updateToggleVisuals(btn, isDark));
    };

    sync();

    toggles.forEach((btn) => {
      btn.addEventListener('click', () => {
        const isDark = document.body.classList.contains('dark-mode');
        const next = !isDark;
        applyTheme(next);
        setStoredTheme(next);
        toggles.forEach((toggleBtn) => updateToggleVisuals(toggleBtn, next));
      });
    });
  };

  const showFlashMessages = () => {
    if (!window.Swal) return;
    document.querySelectorAll('[data-flash-message]').forEach((node) => {
      const message = node.getAttribute('data-flash-message');
      if (!message) return;

      const type = node.getAttribute('data-flash-type') || 'info';
      const explicitTitle = node.getAttribute('data-flash-title');
      let title = explicitTitle;
      if (!title) {
        if (type === 'success') title = 'Success';
        else if (type === 'error') title = 'Something went wrong';
        else if (type === 'warning') title = 'Please note';
        else title = 'Heads up';
      }

      const timer = parseInt(node.getAttribute('data-flash-timer'), 10);
      const timerValue = Number.isFinite(timer) && timer > 0 ? timer : null;

      window.Swal.fire({
        icon: type,
        title,
        text: message,
        timer: timerValue || undefined,
        timerProgressBar: Boolean(timerValue),
        confirmButtonText: node.getAttribute('data-flash-button') || 'Okay',
        customClass: {
          popup: node.getAttribute('data-flash-size') === 'compact' ? 'swal2-popup--compact' : ''
        }
      });

      node.remove();
    });
  };

  const enhanceFormInteractions = () => {
    document.querySelectorAll('.auth-form [data-password-toggle]').forEach((btn) => {
      const input = document.querySelector(btn.getAttribute('data-password-toggle'));
      if (!input) return;
      btn.addEventListener('click', () => {
        const nextType = input.type === 'password' ? 'text' : 'password';
        input.type = nextType;
        const icon = btn.querySelector('i');
        if (icon) {
          icon.classList.toggle('fa-eye');
          icon.classList.toggle('fa-eye-slash');
        }
      });
    });
  };

  const initConfirmDialogs = () => {
    if (!window.Swal) return;
    document.querySelectorAll('[data-confirm]').forEach((trigger) => {
      trigger.addEventListener('click', (event) => {
        event.preventDefault();
        const message = trigger.getAttribute('data-confirm') || 'Do you want to continue?';
        const title = trigger.getAttribute('data-confirm-title') || 'Are you sure?';
        const confirmText = trigger.getAttribute('data-confirm-button') || 'Confirm';
        const cancelText = trigger.getAttribute('data-confirm-cancel') || 'Cancel';
        const icon = trigger.getAttribute('data-confirm-icon') || 'warning';

        window.Swal.fire({
          title,
          text: message,
          icon,
          showCancelButton: true,
          confirmButtonText: confirmText,
          cancelButtonText: cancelText,
          reverseButtons: true
        }).then((result) => {
          if (!result.isConfirmed) return;
          const href = trigger.getAttribute('href');
          if (href) {
            window.location.href = href;
            return;
          }
          const submitTarget = trigger.getAttribute('data-confirm-submit');
          if (submitTarget) {
            const form = document.getElementById(submitTarget);
            if (form) form.submit();
            return;
          }
          const form = trigger.closest('form');
          if (form) {
            form.submit();
          }
        });
      });
    });
  };

  const initProfilePhotoPicker = () => {
    const input = document.getElementById('profilePicInput');
    if (!input) return;
    const hint = document.querySelector('[data-profile-upload-hint]');

    const formatSize = (file) => {
      if (!file) return '';
      const sizeMb = file.size / (1024 * 1024);
      if (sizeMb >= 1) {
        return `${sizeMb.toFixed(sizeMb >= 10 ? 1 : 2)} MB`;
      }
      const sizeKb = Math.max(1, Math.round(file.size / 1024));
      return `${sizeKb} KB`;
    };

    input.addEventListener('change', () => {
      if (!hint) return;
      if (!input.files || !input.files.length) {
        hint.textContent = 'PNG or JPG up to 2MB.';
        return;
      }
      const file = input.files[0];
      hint.textContent = `${file.name} - ${formatSize(file)}`;
    });
  };

  const initialIsDark = determinePreferredTheme();
  applyTheme(initialIsDark);

  ready(() => {
    initThemeToggle();
    showFlashMessages();
    enhanceFormInteractions();
    initConfirmDialogs();
    initProfilePhotoPicker();
  });
})();
