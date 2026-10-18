const stateKey = '__shayyahImagePreview';

export function createImagePreviewHistory(history, getUrl) {
    const isOpen = () => history.state?.[stateKey] === true;

    return {
        isOpen,

        open() {
            if (isOpen()) {
                return;
            }

            const state = history.state && typeof history.state === 'object'
                ? { ...history.state }
                : {};

            state[stateKey] = true;
            history.pushState(state, '', getUrl());
        },

        close() {
            if (!isOpen()) {
                return false;
            }

            history.back();

            return true;
        },

        clearStale() {
            if (!isOpen()) {
                return;
            }

            const state = { ...history.state };
            delete state[stateKey];
            history.replaceState(
                Object.keys(state).length > 0 ? state : null,
                '',
                getUrl(),
            );
        },
    };
}
