/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/js/admin/hide-panel.js":
/*!************************************!*\
  !*** ./src/js/admin/hide-panel.js ***!
  \************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
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

/***/ }),

/***/ "./src/js/admin/index.js":
/*!*******************************!*\
  !*** ./src/js/admin/index.js ***!
  \*******************************/
/***/ (() => {

/**
 * File frontend.js.
 *
 * Handles frontend scripts
 */
(function ($) {
  'use strict';

  // Toggle block users table checkboxes when all blocks are disabled for any specific user roles on md-governance plugin page.
  document.addEventListener("DOMContentLoaded", function () {
    const allBlockUserRoleSelects = document.querySelectorAll('.mdgv_all_blocks_list_user_role_select');
    const blockUsersTableCheckboxes = document.querySelectorAll('.mdgv_block_users_table input[type="checkbox"]');
    let selectedValues = [];
    if (allBlockUserRoleSelects.length === 0 || blockUsersTableCheckboxes.length === 0) {
      return;
    }
    allBlockUserRoleSelects.forEach(select => {
      function disableCheckboxes() {
        if (select.checked) {
          selectedValues.push(select.value);
        } else {
          const index = selectedValues.indexOf(select.value);
          if (index > -1) {
            selectedValues.splice(index, 1);
          }
        }
        blockUsersTableCheckboxes.forEach(checkbox => {
          if (selectedValues.includes(checkbox.value)) {
            checkbox.disabled = true;
          } else {
            checkbox.disabled = false;
          }
        });
      }
      disableCheckboxes();
      select.addEventListener('change', disableCheckboxes);
    });

    // Dismiss notice message
    const dismissNotice = document.querySelector('.mdgv_notice .notice-dismiss');
    if (dismissNotice) {
      dismissNotice.addEventListener('click', () => {
        const notice = document.querySelector('.mdgv_notice');
        if (notice) {
          notice.remove();
        }
      });
    }

    // Save block governance script.
    const blockGovernanceForm = document.getElementById('blockGovernanceForm');
    if (blockGovernanceForm) {
      blockGovernanceForm.addEventListener('submit', event => {
        event.preventDefault();
      });
    }
    const blockGovernanceSubmitButton = document.getElementById('blockGovernanceSubmitButton');
    if (blockGovernanceSubmitButton) {
      blockGovernanceSubmitButton.addEventListener('click', event => {
        event.preventDefault();

        // Save block governance data on submit event.
        saveBlockGovernance();
      });
    }

    // Function is used to save block governance data.
    function saveBlockGovernance() {
      const blockGovernanceForm = document.getElementById('blockGovernanceForm');
      const blockGovernanceFormData = new FormData(blockGovernanceForm);
      const blockGovernanceSubmit = blockGovernanceForm.querySelector('#blockGovernanceSubmitButton');
      const isSpinnerExists = document.getElementById('blockGovernanceSpinner');
      const isSuccessIndicatorExists = document.getElementById('successIndicator');
      const blockGovernanceNotice = document.getElementById('blockGovernanceNotice');
      const blocksByCategory = document.getElementById('searchByCategory').value;

      // Append action to block governance form data.
      blockGovernanceFormData.append('action', 'save_block_governance');
      blockGovernanceFormData.append('blocksByCategory', blocksByCategory);
      if (isSpinnerExists) {
        isSpinnerExists.remove();
      }
      if (isSuccessIndicatorExists) {
        isSuccessIndicatorExists.remove();
      }

      // Create spinner element.
      const blockGovernanceSpinnerElement = document.createElement('span');
      blockGovernanceSpinnerElement.classList.add('spinner');
      blockGovernanceSpinnerElement.setAttribute('id', 'blockGovernanceSpinner');
      blockGovernanceSpinnerElement.style.float = 'none';
      blockGovernanceSpinnerElement.style.margin = '0 0 3px 8px';

      // Create success indicator element.
      const successIndicatorElement = document.createElement('span');
      successIndicatorElement.classList.add('dashicons', 'dashicons-yes-alt');
      successIndicatorElement.setAttribute('id', 'successIndicator');

      // Append the spinner after the submit button element.
      blockGovernanceSubmit.disabled = true;
      blockGovernanceSubmit.insertAdjacentElement('afterend', blockGovernanceSpinnerElement);
      blockGovernanceSpinnerElement.style.visibility = 'visible';

      // Send the AJAX request using fetch method.
      fetch(mdGositeConfig.ajaxUrl, {
        method: 'POST',
        body: blockGovernanceFormData
      }).then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
      }).then(data => {
        blockGovernanceSubmit.disabled = false;
        blockGovernanceSpinnerElement.style.visibility = 'hidden';
        blockGovernanceSubmit.insertAdjacentElement('afterend', successIndicatorElement);
        if (data.success) {
          appendNotice(blockGovernanceNotice, 'success', data.message);
        } else {
          appendNotice(blockGovernanceNotice, 'error', data.message);
        }
      }).catch(error => {
        blockGovernanceSubmit.setAttribute('disabled', false);
        blockGovernanceSpinnerElement.style.visibility = 'hidden';
        appendNotice(blockGovernanceNotice, 'error', error);
        console.error('Error:', error);
      }).finally(() => {
        blockGovernanceSubmit.disabled = false;
        if (blockGovernanceSpinnerElement) {
          blockGovernanceSpinnerElement.remove();
        }
        setTimeout(() => {
          if (successIndicatorElement) {
            successIndicatorElement.style.transition = 'opacity 5s ease';
            successIndicatorElement.style.opacity = '0';
            successIndicatorElement.remove();
          }
        }, 2000);
      });
    }

    // Common function for notice.
    function appendNotice(noticeContainer, type, message) {
      const noticeType = type === 'success' ? 'notice-success' : 'notice-error';
      const existingNotice = noticeContainer.querySelector('.notice');

      // Remove existing notice if present.
      if (existingNotice) {
        existingNotice.remove();
      }
      const notice = document.createElement('div');
      notice.className = `notice ${noticeType} is-dismissible`;
      notice.innerHTML = `<p>${message}</p>`;
      noticeContainer.appendChild(notice);

      // Dismissible notice behavior
      const dismissButton = document.createElement('button');
      dismissButton.type = 'button';
      dismissButton.className = 'notice-dismiss';
      dismissButton.innerHTML = '<span class="screen-reader-text">Dismiss this notice.</span>';
      dismissButton.addEventListener('click', function () {
        notice.style.opacity = '0';
        setTimeout(() => {
          notice.remove();
        }, 100);
      });

      // setTimeout(() => {
      // 	notice.style.transition = 'opacity 5s ease';
      // 	notice.style.opacity = '0';
      // 	notice.remove();
      // }, 4000);

      notice.appendChild(dismissButton);

      // Make the notice dismissible
      notice.addEventListener('click', function (e) {
        if (e.target.classList.contains('notice-dismiss')) {
          notice.style.opacity = '0';
          setTimeout(() => {
            notice.remove();
          }, 100);
        }
      });
    }

    // Number of rows per page
    const rowsPerPage = 12;

    // Select elements
    const searchInput = document.querySelector(".blocks-search-input");
    const blockItems = Array.from(document.querySelectorAll(".mdgv_block_table_item"));
    const paginationContainer = document.querySelector(".pagination-container");
    const tableBody = document.querySelector("table.mdgv_block_users_table tbody"); // Assuming the table rows are in the tbody

    // Add a 'No block found' message row to the table (only once)
    let noResultsRow = document.querySelector(".no-results");
    if (!noResultsRow) {
      noResultsRow = document.createElement("tr");
      noResultsRow.classList.add("no-results");
      noResultsRow.innerHTML = `<td colspan="100%">No block found</td>`;
      noResultsRow.style.display = "none";
      tableBody.appendChild(noResultsRow); // Append the no-results row to the table body
    }

    // Store the filtered items separately from the main items
    let filteredItems = blockItems;

    // Function to render pagination buttons
    function renderPagination(totalRows) {
      paginationContainer.innerHTML = "";
      const totalPages = Math.ceil(totalRows / rowsPerPage);
      for (let i = 1; i <= totalPages; i++) {
        const button = document.createElement("button");
        button.textContent = i;
        button.classList.add("pagination-button");
        button.setAttribute("type", "button");
        if (i === 1) button.classList.add("active"); // Mark the first button as active by default
        button.addEventListener("click", () => goToPage(i));
        paginationContainer.appendChild(button);
      }
    }

    // Function to show rows based on the page number
    function goToPage(pageNumber) {
      const startIndex = (pageNumber - 1) * rowsPerPage;
      const endIndex = startIndex + rowsPerPage;

      // Show only the rows for the current page within the filtered items
      filteredItems.forEach((item, index) => {
        item.style.display = index >= startIndex && index < endIndex ? "" : "none";
      });

      // Update active pagination button
      document.querySelectorAll(".pagination-button").forEach((button, index) => {
        button.classList.toggle("active", index === pageNumber - 1);
      });
    }

    // Function to filter items based on search input
    const categorySelect = document.getElementById('searchByCategory');
    function filterItems() {
      const searchKey = searchInput.value.trim().toLowerCase();
      const selectedCategory = categorySelect.value;
      let hasResults = false;

      // Filter blockItems by search key
      filteredItems = blockItems.filter(item => {
        // const blockTitle = item.querySelector(".mdgv_block_table_title").textContent.toLowerCase().trim();
        const blockTitleAttr = item.getAttribute('data-block-title');
        const blockTitle = blockTitleAttr ? blockTitleAttr.toLowerCase().trim() : ""; // Safe check for null

        const blockCategory = item.getAttribute('data-block-category')?.toLowerCase().trim() || ""; // Safe check for category

        // Check if the block matches both the search key and selected category
        const matchesSearchKey = blockTitle.includes(searchKey);
        const matchesCategory = selectedCategory === "" || blockCategory === selectedCategory; // If no category selected, show all items

        // If both conditions are true, it's a match
        const match = matchesSearchKey && matchesCategory;

        // const match = blockTitle.includes(searchKey);
        if (match) {
          hasResults = true;
        }
        return match;
      });

      // Clear the table before adding new rows (either search results or no results)
      tableBody.innerHTML = "";

      // If there are no results, show the 'No block found' message
      if (!hasResults) {
        tableBody.appendChild(noResultsRow); // Append the no-results message
        noResultsRow.style.display = ""; // Show the no-results message
        paginationContainer.style.display = "none"; // Hide pagination when there are no results
      } else {
        noResultsRow.style.display = "none"; // Hide the no-results message
        paginationContainer.style.display = filteredItems.length > rowsPerPage ? "flex" : "none";

        // Add the filtered items to the table
        filteredItems.forEach(item => {
          tableBody.appendChild(item); // Append the filtered block items to the table
        });

        // Update pagination for filtered results
        renderPagination(filteredItems.length);

        // Go to the first page of the filtered results
        goToPage(1);
      }
    }

    // Listen for category selection change to trigger filtering
    categorySelect.addEventListener('change', filterItems);

    // Event listener for search input
    searchInput.addEventListener("input", filterItems);

    // Initial setup: render pagination for the full list and go to the first page
    filterItems(); // Filter the items first and render pagination correctly
  });
})(jQuery);

/***/ }),

/***/ "./src/scss/admin.scss":
/*!*****************************!*\
  !*** ./src/scss/admin.scss ***!
  \*****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "react/jsx-runtime":
/*!**********************************!*\
  !*** external "ReactJSXRuntime" ***!
  \**********************************/
/***/ ((module) => {

"use strict";
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
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
/*!*************************!*\
  !*** ./src/js/admin.js ***!
  \*************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _scss_admin_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../scss/admin.scss */ "./src/scss/admin.scss");
/* harmony import */ var _admin_index__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./admin/index */ "./src/js/admin/index.js");
/* harmony import */ var _admin_index__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_admin_index__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _admin_hide_panel__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./admin/hide-panel */ "./src/js/admin/hide-panel.js");
// Styles


// Javascript


})();

/******/ })()
;
//# sourceMappingURL=admin.js.map