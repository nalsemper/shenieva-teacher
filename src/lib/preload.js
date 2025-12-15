// Lightweight image preloader utility
// Usage: import { preloadImages } from '$lib/preload';
// await preloadImages(urls, (progress) => { /* progress: 0..1 */ })

/**
 * Preload a list of image URLs.
 * @param {string[]} urls
 * @param {(progress:number) => void} onProgress
 * @returns {Promise<void>}
 */
export function preloadImages(urls = [], onProgress = () => {}) {
  return /** @type {Promise<void>} */ (new Promise((resolve) => {
    const total = urls.length;
    if (total === 0) {
      try { onProgress(1); } catch (e) {}
      resolve();
      return;
    }

    let loaded = 0;

    const doneOne = () => {
      loaded += 1;
      try {
        onProgress(loaded / total);
      } catch (e) {
        // ignore progress callback errors
      }
      if (loaded === total) resolve();
    };

    for (const url of urls) {
      try {
        const img = new Image();
        // allow cross-origin images if served from CDN
        img.crossOrigin = 'anonymous';
        img.onload = doneOne;
        img.onerror = (e) => {
          // still count errors as "loaded" to avoid stalling
          console.warn('Image failed to load:', url, e);
          doneOne();
        };
        img.src = url;
      } catch (err) {
        console.warn('preloadImages error for', url, err);
        doneOne();
      }
    }
  }));
}

/**
 * Preload a single asset (image or audio)
 * @param {string} url
 * @returns {Promise<void>}
 */
function preloadAsset(url) {
  return new Promise((resolve) => {
    try {
      // Check if it's an audio file
      if (url.match(/\.(mp3|ogg|wav|m4a)$/i)) {
        const audio = document.createElement('audio');
        audio.preload = 'auto';
        audio.oncanplaythrough = () => resolve();
        audio.onerror = (e) => {
          console.warn('Audio failed to load:', url, e);
          resolve();
        };
        audio.src = url;
        audio.load();
      } else {
        // Assume it's an image
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = () => resolve();
        img.onerror = (e) => {
          console.warn('Image failed to load:', url, e);
          resolve();
        };
        img.src = url;
      }
    } catch (err) {
      console.warn('preloadAsset error for', url, err);
      resolve();
    }
  });
}

// Helper: progressively preload in batches to reduce memory spike
/**
 * @param {string[]} urls
 * @param {(progress:number) => void} onProgress
 * @param {number} batchSize
 */
export async function preloadImagesBatched(urls = [], onProgress = () => {}, batchSize = 20) {
  const total = urls.length;
  let done = 0;
  for (let i = 0; i < urls.length; i += batchSize) {
    const slice = urls.slice(i, i + batchSize);
    // Preload each asset in the batch
    await Promise.all(slice.map(url => preloadAsset(url)));
    done += slice.length;
    onProgress(done / total);
  }
}
