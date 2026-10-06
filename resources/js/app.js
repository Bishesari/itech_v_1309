import FingerprintJS from '@fingerprintjs/fingerprintjs';

let fingerprintPromise = null;

window.getFingerprint = async function () {
    fingerprintPromise ??= FingerprintJS.load();

    const fp = await fingerprintPromise;
    const result = await fp.get();

    return result.visitorId;
};
