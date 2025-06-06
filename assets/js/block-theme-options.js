(function (wp) {
  const { addFilter } = wp.hooks;
  const { InspectorControls } = wp.blockEditor;
  const { PanelBody, SelectControl } = wp.components;
  const { createElement, Fragment } = wp.element;
  const { createHigherOrderComponent } = wp.compose;

  // add attributes
  addFilter(
    'blocks.registerBlockType',
    'sunflower/theme-options-attributes',
    function (settings, name) {
      if (!name.startsWith('core/')) return settings;

      settings.attributes = Object.assign({}, settings.attributes, {
        themeColor: { type: 'string', default: '' },
        themeForm: { type: 'string', default: '' },
      });
      return settings;
    }
  );

  // ui in editor
  const withThemeOptions = createHigherOrderComponent(function (BlockEdit) {
    return function (props) {
      const { attributes, setAttributes, name } = props;
      const { themeColor, themeForm } = attributes;

      if (!name.startsWith('core/')) {
        return createElement(BlockEdit, props);
      }

      return createElement(
        Fragment,
        {},
        createElement(BlockEdit, props),
        createElement(
          InspectorControls,
          {},
          createElement(
            PanelBody,
            { title: 'Block options', initialOpen: true },
            createElement(SelectControl, {
              label: 'Color scheme',
              value: themeColor,
              options: [
                { label: 'global', value: '' },
                { label: 'light', value: 'light' },
                { label: 'green', value: 'green' },
              ],
              onChange: function (val) {
                setAttributes({ themeColor: val });
              },
            }),
            createElement(SelectControl, {
              label: 'Form style',
              value: themeForm,
              options: [
                { label: 'global', value: '' },
                { label: 'rounded', value: 'rounded' },
                { label: 'sharp', value: 'sharp' },
              ],
              onChange: function (val) {
                setAttributes({ themeForm: val });
              },
            })
          )
        )
      );
    };
  }, 'withThemeOptions');

  addFilter(
    'editor.BlockEdit',
    'sunflower/theme-options-ui',
    withThemeOptions
  );

  // class in HTML
  addFilter(
    'blocks.getSaveContent.extraProps',
    'sunflower/theme-options-classes',
    function (extraProps, blockType, attributes) {
      const { themeColor, themeForm } = attributes;

      const extraClass = [
        themeColor ? 'colorscheme-' + themeColor : '',
        themeForm ? 'formstyle-' + themeForm : '',
      ]
        .filter(Boolean)
        .join(' ');

      if (extraClass) {
        extraProps.className = (extraProps.className || '') + ' ' + extraClass;
      }

      return extraProps;
    }
  );
})(window.wp);
