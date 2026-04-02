/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ 20:
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

var __webpack_unused_export__;
/**
 * @license React
 * react-jsx-runtime.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */
var f=__webpack_require__(609),k=Symbol.for("react.element"),l=Symbol.for("react.fragment"),m=Object.prototype.hasOwnProperty,n=f.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,p={key:!0,ref:!0,__self:!0,__source:!0};
function q(c,a,g){var b,d={},e=null,h=null;void 0!==g&&(e=""+g);void 0!==a.key&&(e=""+a.key);void 0!==a.ref&&(h=a.ref);for(b in a)m.call(a,b)&&!p.hasOwnProperty(b)&&(d[b]=a[b]);if(c&&c.defaultProps)for(b in a=c.defaultProps,a)void 0===d[b]&&(d[b]=a[b]);return{$$typeof:k,type:c,key:e,ref:h,props:d,_owner:n.current}}__webpack_unused_export__=l;exports.jsx=q;__webpack_unused_export__=q;


/***/ }),

/***/ 609:
/***/ ((module) => {

module.exports = window["React"];

/***/ }),

/***/ 848:
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {



if (true) {
  module.exports = __webpack_require__(20);
} else // removed by dead control flow
{}


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
var __webpack_exports__ = {};

;// external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// external ["wp","coreData"]
const external_wp_coreData_namespaceObject = window["wp"]["coreData"];
;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// ./src/js/blocks/constants.js
/**
 * WordPress dependencies
 */


/**
 * Team member entity.
 *
 * @type {Object}
 */
const TEAM_MEMBER_ENTITY = {
  name: 'team_member',
  kind: 'root',
  baseURL: '/wc-bookings/v2/resources/team-members',
  label: (0,external_wp_i18n_namespaceObject.__)('Team Member', 'woocommerce-bookings'),
  plural: (0,external_wp_i18n_namespaceObject.__)('Team Members', 'woocommerce-bookings'),
  key: 'id',
  supportsPagination: true
};
;// external ["wc","wcBlocksRegistry"]
const external_wc_wcBlocksRegistry_namespaceObject = window["wc"]["wcBlocksRegistry"];
;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// ./node_modules/@wordpress/icons/build-module/icon/index.js
/**
 * WordPress dependencies
 */


/**
 * External dependencies
 */

/**
 * Return an SVG icon.
 *
 * @param props The component props.
 *
 * @return Icon component
 */
/* harmony default export */ const icon = ((0,external_wp_element_namespaceObject.forwardRef)(({
  icon,
  size = 24,
  ...props
}, ref) => {
  return (0,external_wp_element_namespaceObject.cloneElement)(icon, {
    width: size,
    height: size,
    ...props,
    ref
  });
}));
//# sourceMappingURL=index.js.map
;// external ["wp","primitives"]
const external_wp_primitives_namespaceObject = window["wp"]["primitives"];
// EXTERNAL MODULE: ./node_modules/react/jsx-runtime.js
var jsx_runtime = __webpack_require__(848);
;// ./node_modules/@wordpress/icons/build-module/library/scheduled.js
/**
 * WordPress dependencies
 */


const scheduled = /*#__PURE__*/(0,jsx_runtime.jsx)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24",
  children: /*#__PURE__*/(0,jsx_runtime.jsx)(external_wp_primitives_namespaceObject.Path, {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M12 18.5a6.5 6.5 0 1 1 0-13 6.5 6.5 0 0 1 0 13ZM4 12a8 8 0 1 1 16 0 8 8 0 0 1-16 0Zm9 1V8h-1.5v3.5h-2V13H13Z"
  })
});
/* harmony default export */ const library_scheduled = (scheduled);
//# sourceMappingURL=scheduled.js.map
;// ./src/js/blocks/product-collections/services.jsx
/**
 * External dependencies
 */




/**
 * Default inner block template for product collections.
 */

const INNER_BLOCKS_PRODUCT_TEMPLATE = ['woocommerce/product-template', {}, [['woocommerce/product-image', {
  imageSizing: 'thumbnail'
}], ['core/post-title', {
  textAlign: 'center',
  level: 3,
  fontSize: 'medium',
  style: {
    spacing: {
      margin: {
        bottom: '0.75rem',
        top: '0'
      }
    }
  },
  isLink: true,
  __woocommerceNamespace: 'core/post-title/product-title'
}], ['woocommerce/product-price', {
  textAlign: 'center',
  fontSize: 'small'
}], ['woocommerce-bookings/booking-location', {
  textAlign: 'center'
}], ['woocommerce/product-button', {
  textAlign: 'center',
  fontSize: 'small'
}]]];

/**
 * Construct the inner blocks for the collection.
 */
const heading = ['core/heading', {
  textAlign: 'center',
  level: 2,
  content: (0,external_wp_i18n_namespaceObject.__)('Services', 'woocommerce-bookings'),
  style: {
    spacing: {
      margin: {
        bottom: '1rem'
      }
    }
  }
}];
const innerBlocks = [heading, INNER_BLOCKS_PRODUCT_TEMPLATE];

/**
 * Setup Attributes.
 */
const attributes = {
  displayLayout: {
    type: 'flex',
    columns: 5,
    shrinkColumns: true
  },
  query: {
    perPage: 5,
    pages: 1
  },
  hideControls: ['filterable', 'inherit', 'hand-picked']
};

/**
 * Arguments to register the collection.
 */
const collection = {
  name: 'woocommerce-bookings/product-collection/services',
  title: (0,external_wp_i18n_namespaceObject.__)('Services', 'woocommerce-bookings'),
  icon: /*#__PURE__*/(0,jsx_runtime.jsx)(icon, {
    icon: library_scheduled
  }),
  description: (0,external_wp_i18n_namespaceObject.__)('Display a list of bookable service products.', 'woocommerce-bookings'),
  keywords: ['services', 'bookable', 'product collection'],
  scope: ['block', 'inserter']
};

/**
 * Construct and export.
 */
const servicesCollectionData = {
  ...collection,
  attributes,
  innerBlocks
};

/**
 * Register product collection type.
 */
(0,external_wc_wcBlocksRegistry_namespaceObject.__experimentalRegisterProductCollection)(servicesCollectionData);
;// external ["wp","blocks"]
const external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// ./src/js/blocks/block-bindings/team-members.jsx
/**
 * WordPress dependencies
 */




/**
 * Get list of available fields for the team member source.
 * This provides the dropdown options in the block bindings UI.
 *
 * @param {Object} options         Options object.
 * @param {Object} options.context Block context (postId, postType, etc.).
 * @return {Array} Array of field objects with label, type, and args.
 */
function getFieldsList({
  context
}) {
  // Only show fields for team member post type.
  const postType = context?.postType;
  if (!postType || 'bookable_team_member' !== postType) {
    return [];
  }
  return [{
    label: (0,external_wp_i18n_namespaceObject.__)('Team member name', 'woocommerce-bookings'),
    type: 'string',
    args: {
      field: 'name'
    }
  }, {
    label: (0,external_wp_i18n_namespaceObject.__)('Team member email', 'woocommerce-bookings'),
    type: 'string',
    args: {
      field: 'email'
    }
  }, {
    label: (0,external_wp_i18n_namespaceObject.__)('Team member phone number', 'woocommerce-bookings'),
    type: 'string',
    args: {
      field: 'phone_number'
    }
  }, {
    label: (0,external_wp_i18n_namespaceObject.__)('Team member description', 'woocommerce-bookings'),
    type: 'string',
    args: {
      field: 'description'
    }
  }];
}

/**
 * Register the team member block bindings source with UI support.
 */
(0,external_wp_blocks_namespaceObject.registerBlockBindingsSource)({
  name: 'woocommerce-bookings/team-member',
  label: (0,external_wp_i18n_namespaceObject.__)('Team Member', 'woocommerce-bookings'),
  getFieldsList,
  getValues: ({
    bindings,
    select,
    context
  }) => {
    const field = bindings.content?.args?.field;
    const store = select(external_wp_coreData_namespaceObject.store);
    const record = store.getEntityRecord('root', 'team_member', context.postId);
    if (!record) {
      let placeholder = '';
      switch (field) {
        case 'name':
          placeholder = (0,external_wp_i18n_namespaceObject.__)('Name', 'woocommerce-bookings');
          break;
        case 'email':
          placeholder = (0,external_wp_i18n_namespaceObject.__)('Email', 'woocommerce-bookings');
          break;
        case 'phone_number':
          placeholder = (0,external_wp_i18n_namespaceObject.__)('Phone number', 'woocommerce-bookings');
          break;
        case 'description':
          placeholder = (0,external_wp_i18n_namespaceObject.__)('Description', 'woocommerce-bookings');
          break;
        default:
          placeholder = (0,external_wp_i18n_namespaceObject.__)('Team Member Data', 'woocommerce-bookings');
          break;
      }
      return {
        content: placeholder
      };
    }

    // Proceed to live preview.
    switch (field) {
      case 'name':
        return {
          content: record.name
        };
      case 'email':
        return {
          content: record.email
        };
      case 'phone_number':
        return {
          content: record.phone_number
        };
      case 'description':
        return {
          content: record.description
        };
      default:
        return {
          content: record.name
        };
    }
  },
  usesContext: ['postId', 'postType']
});
;// ./src/js/blocks/editor.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */

const registerTeamMemberEntity = () => {
  const {
    addEntities
  } = (0,external_wp_data_namespaceObject.dispatch)(external_wp_coreData_namespaceObject.store);
  addEntities([TEAM_MEMBER_ENTITY]);
};
registerTeamMemberEntity();

/**
 * Register blocks.
 */


/******/ })()
;
//# sourceMappingURL=block-editor.js.map