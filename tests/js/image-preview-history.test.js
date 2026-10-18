import assert from 'node:assert/strict';
import test from 'node:test';
import { createImagePreviewHistory } from '../../resources/js/image-preview-history.js';

function createMockHistory(initialState = null) {
    const entries = [{ state: initialState, url: 'https://example.test/topic' }];
    let index = 0;
    let backCalls = 0;

    return {
        get state() {
            return entries[index].state;
        },

        get backCalls() {
            return backCalls;
        },

        pushState(state, _title, url) {
            entries.splice(index + 1);
            entries.push({ state, url });
            index += 1;
        },

        replaceState(state, _title, url) {
            entries[index] = { state, url };
        },

        back() {
            backCalls += 1;

            if (index > 0) {
                index -= 1;
            }
        },
    };
}

test('closing an image preview backs up exactly one history entry', () => {
    const history = createMockHistory({ page: 'topic' });
    const imageHistory = createImagePreviewHistory(history, () => 'https://example.test/topic');

    imageHistory.open();
    imageHistory.close();

    assert.equal(history.backCalls, 1);
    assert.deepEqual(history.state, { page: 'topic' });
    assert.equal(imageHistory.isOpen(), false);
});

test('opening repeatedly does not add extra history entries', () => {
    const history = createMockHistory();
    const imageHistory = createImagePreviewHistory(history, () => 'https://example.test/topic');

    imageHistory.open();
    imageHistory.open();
    imageHistory.close();

    assert.equal(history.backCalls, 1);
});

test('closing without an image preview does not navigate', () => {
    const history = createMockHistory();
    const imageHistory = createImagePreviewHistory(history, () => 'https://example.test/topic');

    assert.equal(imageHistory.close(), false);
    assert.equal(history.backCalls, 0);
});

test('stale image-preview state is removed without changing other history state', () => {
    const history = createMockHistory({ page: 'topic', __shayyahImagePreview: true });
    const imageHistory = createImagePreviewHistory(history, () => 'https://example.test/topic');

    imageHistory.clearStale();

    assert.deepEqual(history.state, { page: 'topic' });
    assert.equal(imageHistory.isOpen(), false);
});
