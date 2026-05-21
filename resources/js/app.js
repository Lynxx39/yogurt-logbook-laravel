// Main application entry for resources/js
import { initStageValidation } from './yogurt-validation';

document.addEventListener('DOMContentLoaded', () => {
	// initialize validation for stage forms
	try { initStageValidation(); } catch (err) { console.error('initStageValidation error', err); }
});

// export for potential external use
export { initStageValidation };
