import './bootstrap';
import { createImagePreviewHistory } from './image-preview-history';

window.imagePreviewHistory = createImagePreviewHistory(window.history, () => window.location.href);
window.imagePreviewHistory.clearStale();
