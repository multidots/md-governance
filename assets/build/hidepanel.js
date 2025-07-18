/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "react/jsx-runtime":
/*!**********************************!*\
  !*** external "ReactJSXRuntime" ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["ReactJSXRuntime"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!************************************!*\
  !*** ./src/js/admin/hide-panel.js ***!
  \************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__);

(function (wp, mdGovernanceSettings) {
  // Ensure the code runs only if the block editor data is available
  if (typeof mdGovernanceSettings !== 'undefined' && mdGovernanceSettings.isBlockEditor) {
    const {
      createHigherOrderComponent
    } = wp.compose;
    const {
      useEffect,
      Fragment,
      Children
    } = wp.element;
    const {
      addFilter
    } = wp.hooks;
    const {
      useSelect,
      dispatch
    } = wp.data;
    const {
      InspectorControls
    } = wp.blockEditor;
    const restrictedBlocks = mdGovernanceSettings?.restrictedBlocks || [];

    // HOC to handle restricted access
    const withRestrictedAccessMessage = createHigherOrderComponent(BlockEdit => {
      return props => {
        const {
          name,
          clientId
        } = props;
        const isRestricted = restrictedBlocks.includes(name);
        const isSelected = useSelect(select => select('core/block-editor').isBlockSelected(clientId));

        // Deselect block if it is restricted
        useEffect(() => {
          if (isSelected && isRestricted) {
            dispatch('core/block-editor').clearSelectedBlock();
          }
        }, [isSelected, isRestricted]);

        // Render the block normally if not restricted
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)(BlockEdit, {
          ...props
        });
      };
    }, 'withRestrictedAccessMessage');

    // Add the HOC and control filter for BlockEdit
    addFilter('editor.BlockEdit', 'md-governance/with-restricted-access-message', withRestrictedAccessMessage);

    // HOC to handle restricted access and class addition
    const withRestrictedBlockClass = createHigherOrderComponent(BlockListBlock => {
      return props => {
        const {
          name
        } = props;

        // Update restricted blocks list
        const restrictedBlocks = mdGovernanceSettings?.restrictedBlocks || [];
        const isRestricted = restrictedBlocks.includes(name);
        const blockProps = {
          ...props,
          className: isRestricted ? `md_restricted_block` : props.className
        };
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)(BlockListBlock, {
          ...blockProps
        });
      };
    }, 'withRestrictedBlockClass');

    // Add the HOC and control filter for BlockListBlock
    addFilter('editor.BlockListBlock', 'md-governance/with-restricted-block-class', withRestrictedBlockClass);
  }
})(window.wp, window.mdGovernanceSettings);
})();

/******/ })()
;
//# sourceMappingURL=hidepanel.js.map