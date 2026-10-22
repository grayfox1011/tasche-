/**
 * Tasche Piene — opt-in bridge for Divi's existing Design controls.
 *
 * Existing attributes are deliberately NOT promoted to overrides. A user field
 * edit records its changed values or explicitly chosen leaf (even unchanged).
 * No mask means no extra CSS.
 * Keep this schema in sync with inc/divi-design-bridge.php.
 */
(function (root, factory) {
  'use strict';
  var api = factory(root);
  if (typeof module === 'object' && module.exports) module.exports = api;
  if (root && root.document) {
    root.TPDiviDesignBridge = api;
    api.install();
  }
}(typeof window !== 'undefined' ? window : null, function (root) {
  'use strict';

  var MASK = 'tpDesignOverrides';
  var GROUPS = ['background', 'font', 'bodyFont', 'headingFont', 'icon',
    'spacing', 'sizing', 'border', 'boxShadow', 'filters', 'transform',
    'position', 'zIndex', 'overflow', 'layout', 'order', 'button', 'transition'];
  var active = Object.create(null);
  var registries = Object.create(null);
  var installed = false;
  var componentCache = typeof WeakMap === 'function' ? new WeakMap() : null;

  function plain(value) {
    return value && typeof value.toJS === 'function' ? value.toJS() : value;
  }

  function clone(value) {
    if (value === undefined) return undefined;
    return JSON.parse(JSON.stringify(plain(value)));
  }

  function equal(a, b) {
    if (a === b) return true;
    if (Array.isArray(a) && Array.isArray(b)) {
      return a.length === b.length && a.every(function (v, i) { return equal(v, b[i]); });
    }
    if (a && b && typeof a === 'object' && typeof b === 'object') {
      var keys = Object.keys(a);
      return keys.length === Object.keys(b).length && keys.every(function (key) {
        return Object.prototype.hasOwnProperty.call(b, key) && equal(a[key], b[key]);
      });
    }
    return false;
  }

  function safePath(path) {
    return typeof path === 'string' && path.split('.').every(function (key) {
      return key && key !== '__proto__' && key !== 'prototype' && key !== 'constructor';
    });
  }

  function supportedPath(path) {
    if (!safePath(path)) return false;
    var parts = path.split('.');
    return !/^tpDesign/.test(parts[0]) && parts[1] === 'decoration' &&
      GROUPS.indexOf(parts[2]) !== -1 && parts.length > 4;
  }

  function get(object, path) {
    if (!safePath(path)) return undefined;
    return path.split('.').reduce(function (value, key) {
      return value && Object.prototype.hasOwnProperty.call(value, key) ? value[key] : undefined;
    }, object);
  }

  function set(object, path, value) {
    if (!safePath(path)) return;
    var parts = path.split('.');
    var target = object;
    parts.forEach(function (key, index) {
      if (index === parts.length - 1) target[key] = clone(value);
      else {
        if (!target[key] || typeof target[key] !== 'object') target[key] = {};
        target = target[key];
      }
    });
  }

  function meaningful(value) {
    return value !== undefined && value !== null && value !== '' &&
      (typeof value !== 'object' || Object.keys(value).length > 0);
  }

  // Arrays (for example gradient stops) form one atomic native field value.
  function flatten(object, prefix, result) {
    result = result || {};
    Object.keys(object || {}).forEach(function (key) {
      var path = prefix ? prefix + '.' + key : key;
      if (!safePath(path) || /^tpDesign/.test(path.split('.')[0])) return;
      var value = object[key];
      if (value && typeof value === 'object' && !Array.isArray(value)) flatten(value, path, result);
      else if (supportedPath(path)) result[path] = value;
    });
    return result;
  }

  function validValues(attrs) {
    var saved = attrs && attrs[MASK];
    var values = {};
    if (!saved || saved.version !== 1 || !saved.values || typeof saved.values !== 'object') return values;
    Object.keys(saved.values).forEach(function (path) {
      var value = saved.values[path];
      // Reset/paste/import must never resurrect a stale saved override.
      if (supportedPath(path) && meaningful(value) && equal(get(attrs, path), value)) values[path] = clone(value);
    });
    return values;
  }

  function fieldPrefix(context) {
    if (!context || !context.attrName) return '';
    var features = context.features || {};
    var breakpoint = context.responsiveMode || (features.responsive === false ? 'desktop' : context.appBreakpoint) || 'desktop';
    var state = context.stateMode || context.appState || 'value';
    if ((state === 'hover' && features.hover === false) || (state === 'sticky' && features.sticky === false)) state = 'value';
    return context.attrName + '.' + breakpoint + '.' + state +
      ((context.attrSubName || context.subName) ? '.' + (context.attrSubName || context.subName) : '');
  }

  function diffMask(before, after, mode, context) {
    var values = validValues(before || {});
    var oldLeaves = flatten(before || {});
    var newLeaves = flatten(after || {});
    var keys = Object.keys(oldLeaves).concat(Object.keys(newLeaves));
    keys.forEach(function (path) {
      if (equal(oldLeaves[path], newLeaves[path])) return;
      if (mode !== 'reset' && meaningful(newLeaves[path])) values[path] = clone(newLeaves[path]);
      else delete values[path];
    });
    // Explicitly entering the current saved value is still an edit: its old
    // value may have been hidden by legacy CSS. Promote only this exact native
    // leaf, never the rest of the font/spacing/background group. Compound
    // controls without a scalar/array leaf continue to rely on the actual diff.
    if (mode !== 'reset' && context && Object.prototype.hasOwnProperty.call(context, 'inputValue') && meaningful(context.inputValue)) {
      var explicitPath = fieldPrefix(context);
      var explicitValue = get(after, explicitPath);
      if (supportedPath(explicitPath) && meaningful(explicitValue) &&
        (typeof explicitValue !== 'object' || Array.isArray(explicitValue))) {
        values[explicitPath] = clone(explicitValue);
      }
    }
    if (mode === 'reset') {
      var prefix = fieldPrefix(context);
      Object.keys(values).forEach(function (path) {
        if (prefix && (path === prefix || path.indexOf(prefix + '.') === 0)) delete values[path];
      });
    }
    Object.keys(values).forEach(function (path) {
      if (!equal(get(after, path), values[path])) delete values[path];
    });
    return { version: 1, values: values };
  }

  function sparseAttrs(attrs) {
    var sparse = {};
    var values = validValues(attrs || {});
    Object.keys(values).forEach(function (path) { set(sparse, path, values[path]); });
    return sparse;
  }

  function data() { return root && ((root.divi && root.divi.data) || (root.wp && root.wp.data)); }
  function react() { return root && ((root.vendor && root.vendor.React) || (root.wp && root.wp.element) || root.React); }
  function hooks() { return root && ((root.vendor && root.vendor.wp && root.vendor.wp.hooks) || (root.wp && root.wp.hooks)); }
  function moduleApi() { return root && root.divi && root.divi.module; }

  function moduleAttrs(id) {
    var store = data().select('divi/edit-post');
    return clone(plain(store.getModuleAttrs(id))) || {};
  }

  function hasProjectClass(attrs) {
    var html = get(attrs, 'module.advanced.htmlAttributes.desktop.value') || {};
    return /(?:^|\s)tp[-_][\w-]+/.test(String(html.class || '') + ' ' + String(html.id || ''));
  }

  function isProjectModule(id) {
    var store = data().select('divi/edit-post');
    var seen = {};
    for (var depth = 0; id && depth < 30 && !seen[id]; depth += 1) {
      seen[id] = true;
      var item = plain(store.getModule(id));
      if (!item) return false;
      var attrs = plain(item.props && item.props.attrs) || moduleAttrs(id);
      if (/^(?:tp\/|tasche[-_]piene\/)/.test(item.name || '') || hasProjectClass(attrs)) return true;
      id = typeof store.getParentModuleId === 'function' ? store.getParentModuleId(id) : item.parent;
    }
    return false;
  }

  function capture(callback, args, context, mode, receiver) {
    if (typeof callback !== 'function') return undefined;
    var id = context && context.moduleId;
    var name = context && context.attrName;
    if (!data() || !id || !name || name.indexOf('.decoration.') === -1 ||
      /^tpDesign/.test(name) || context.isActivePreset || context.isActiveOptionGroupPreset || active[id] || !isProjectModule(id)) {
      return callback.apply(receiver, args);
    }
    var before = moduleAttrs(id);
    var result;
    var run = function () {
      active[id] = true;
      try {
        result = callback.apply(receiver, args);
        var after = moduleAttrs(id);
        var intent = Object.assign({}, context);
        if (args[0] && typeof args[0] === 'object' && Object.prototype.hasOwnProperty.call(args[0], 'inputValue')) {
          intent.inputValue = args[0].inputValue;
        }
        var mask = diffMask(before, after, mode, intent);
        var previous = after[MASK] || { version: 1, values: {} };
        if (!equal(previous, mask)) {
          // Native attribute edits use the same 500 ms history debounce. Both
          // writes happen synchronously in one registry batch, so its snapshot
          // contains the native value and mask together (including redo).
          data().dispatch('divi/edit-post').editModuleAttribute({
            id: id, attrName: MASK, value: mask, caller: 'user'
          });
        }
      } finally { delete active[id]; }
    };
    var registry = registries[id] || data();
    if (typeof registry.batch === 'function') registry.batch(run);
    else run();
    return result;
  }

  function wrapFields(fields) {
    var result = Object.assign({}, fields);
    Object.keys(result).forEach(function (key) {
      var field = result[key];
      if (!field || typeof field.attrName !== 'string' || field.attrName.indexOf('.decoration.') === -1 || /^tpDesign/.test(field.attrName)) return;
      var copy = Object.assign({}, field);
      ['onChange', 'onReset'].forEach(function (eventName) {
        var native = field[eventName];
        if (native && native.tpDesignBridge) return;
        var wrapped = function (params) {
          var callbacks = moduleApi() && moduleApi()[eventName === 'onChange' ? 'onChangeCallbacks' : 'onResetCallbacks'];
          var fallback = callbacks && (callbacks[field.component && field.component.name] || callbacks['divi/default']);
          return capture(typeof native === 'function' ? native : fallback, [params], params,
            eventName === 'onReset' ? 'reset' : 'change', this);
        };
        wrapped.tpDesignBridge = true;
        copy[eventName] = wrapped;
      });
      result[key] = copy;
    });
    return result;
  }

  // This documented field-library filter also covers background color and
  // gradient, whose native groups do not expose a group.fields filter.
  function wrapFieldComponent(Component) {
    var React = react();
    if (!Component || !React || Component.tpDesignBridge ||
      (typeof Component !== 'function' && typeof Component !== 'object')) return Component;
    if (componentCache && componentCache.has(Component)) return componentCache.get(Component);
    var render = function (props, ref) {
      // Divi's data facade omits batch; use the native registry associated with
      // the field's React provider, including for its adjacent reset control.
      var wpData = root && root.vendor && root.vendor.wp && root.vendor.wp.data;
      var registry = wpData && typeof wpData.useRegistry === 'function' ? wpData.useRegistry() : null;
      if (registry && props.moduleId) registries[props.moduleId] = registry;
      var next = Object.assign({}, props);
      ['onChange', 'onReset'].forEach(function (eventName) {
        if (typeof props[eventName] !== 'function') return;
        next[eventName] = function () {
          return capture(props[eventName], Array.prototype.slice.call(arguments), props,
            eventName === 'onReset' ? 'reset' : 'change', this);
        };
      });
      if (ref) next.ref = ref;
      return React.createElement(Component, next);
    };
    var Wrapped = typeof React.forwardRef === 'function' ? React.forwardRef(render) : function (props) { return render(props); };
    // FieldContainer selects native change/reset handlers using this static.
    Wrapped.fieldName = Component.fieldName;
    Wrapped.displayName = 'TPDesign(' + (Component.displayName || Component.name || Component.fieldName || 'Field') + ')';
    Wrapped.tpDesignBridge = true;
    if (componentCache) componentCache.set(Component, Wrapped);
    return Wrapped;
  }

  function renderOverrides(original, args) {
    var React = react();
    var api = moduleApi();
    if (!React || !api || !api.StyleContainer || !args || !args.elements || !args.attrs) return original;
    // The parsed store, rather than merged module defaults/presets, is the
    // source of truth for validating resets and stale masks.
    var raw = data() && args.id ? moduleAttrs(args.id) : args.attrs;
    var sparse = sparseAttrs(raw);
    var names = Object.keys(sparse);
    if (!names.length) return original;
    var children = [React.createElement(CSSLiteral, { key: 'tp-layer-open', value: '@layer tp-authored {' })];
    names.forEach(function (name) {
      var decoration = sparse[name].decoration;
      var styleProps = { attrsFilter: function () { return clone(decoration); }, advancedStyles: [] };
      Object.keys(decoration).forEach(function (group) { styleProps[group] = { important: true }; });
      var element = args.elements.style({ attrName: name, styleProps: styleProps, isMergeRecursiveProps: true });
      // Avoid defaultPrinted values suppressing a deliberately authored value
      // merely because it matches a Divi default. Keep native selectors intact.
      var props = { key: 'tp-design-' + name, defaultPrintedStyleAttrs: {} };
      // The legacy theme styles p/GSAP line nodes explicitly. Applying a font
      // to their container alone would merely inherit behind those rules.
      ['font', 'bodyFont'].forEach(function (group) {
        if (!decoration[group]) return;
        var previous = element.props && element.props[group] || {};
        var nativeSelector = previous.selectorFunction;
        props[group] = Object.assign({}, previous, {
          selectorFunction: function (params) {
            var selector = typeof nativeSelector === 'function' ? nativeSelector(params) : params.selector;
            return extendTypographySelectors(selector);
          }
        });
      });
      children.push(React.cloneElement(element, props));
    });
    children.push(React.createElement(CSSLiteral, { key: 'tp-layer-close', value: '}' }));
    // Native ElementStyle produces CSS fragments, not <style> tags. Divi's
    // StyleContainer clones every child, so delimiters must be valid React
    // components: cloneElement(string) creates an invalid type in React 18.
    var style = React.createElement(api.StyleContainer, { key: 'tp-design-overrides', mode: 'builder', state: args.state }, children);
    return React.createElement(React.Fragment, null, original, style);
  }

  function CSSLiteral(props) { return props.value; }

  function extendTypographySelectors(selector) {
    if (typeof selector !== 'string' || !selector) return selector;
    var selectors = [];
    var start = 0;
    var depth = 0;
    for (var i = 0; i < selector.length; i += 1) {
      if (selector[i] === '(' || selector[i] === '[') depth += 1;
      if (selector[i] === ')' || selector[i] === ']') depth -= 1;
      if (selector[i] === ',' && depth === 0) { selectors.push(selector.slice(start, i)); start = i + 1; }
    }
    selectors.push(selector.slice(start));
    return selector + ', ' + selectors.map(function (part) {
      return part.trim() + ' :is(p,.tp-split-text__line,.tp-split-text__line-inner,.tp-split-line,.tp-intro-button__label)';
    }).join(', ');
  }

  function install() {
    var h = hooks();
    if (installed || !h || typeof h.addFilter !== 'function') return false;
    installed = true;
    h.addFilter('divi.moduleLibrary.moduleAttributes', 'tasche-piene/design-attribute', function (attrs) {
      return Object.assign({}, attrs, { tpDesignOverrides: { type: 'object' } });
    });
    ['font', 'spacing', 'sizing', 'border', 'boxShadow', 'filters', 'transform',
      'position', 'positionSettings', 'overflow', 'zIndex', 'layout', 'order', 'transition',
      'button', 'button.icon', 'icon', 'text', 'textShadow', 'formField',
      'background.image', 'background.video', 'background.mask', 'background.pattern'].forEach(function (group) {
      h.addFilter('divi.module.options.' + group + '.group.fields', 'tasche-piene/design-fields', wrapFields);
    });
    h.addFilter('divi.fieldLibrary.getFieldComponent', 'tasche-piene/design-field-component', wrapFieldComponent);
    h.addFilter('divi.module.wrapper.render', 'tasche-piene/design-preview', renderOverrides);
    return true;
  }

  return { version: 1, install: install, diffMask: diffMask, sparseAttrs: sparseAttrs,
    validValues: validValues, supportedPath: supportedPath, wrapFields: wrapFields,
    wrapFieldComponent: wrapFieldComponent, renderOverrides: renderOverrides };
}));
