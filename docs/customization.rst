Customization
=============


Add type rendering functions
----------------------------

Standard type rendering functions are available in ``htdocs/js/value-renderer.js`` and have always the same form: ``ldap<Type>TypeRenderer``, with <Type> an attribute type as defined in the attributes_map array in ``config.inc.php``

You can add a new type by adding the appropriate function in ``htdocs/js/value-renderer.js``:

.. code-block:: javascript

    function ldapCustomTypeRenderer(config_js, dn, value, column, type)
    {
        var render = "";
        var val = "test";

        [
         search_result_show_undefined,
         display_show_undefined,
         truncate_value_after,
         search,
         js_date_specifiers
        ] = get_normalized_parameters(config_js);

        var values = {
          "value": val
        };
        render = renderTemplate(arguments.callee.name, values);

        return render;
    }


Then, you just have to create the corresponding template in ``js-templates/ldapCustomTypeRenderer.html``:

.. code-block:: html

    <template id="ldapCustomTypeRenderer">
      {value}
    </template>



Customize type rendering functions
----------------------------------

The type rendering functions are available in ``htdocs/js/value-renderer.js`` and are named: ``ldap<Type>TypeRenderer``, with <Type> an attribute type as defined in the attributes_map array in ``config.inc.php``

You can customize the behaviour of type rendering functions by using `jQuery <https://api.jquery.com/>`_ events.

Here is an example of overloading for the Mailto type rendering function. You can define this event in ``htdocs/js/service-desk.js``

.. code-block:: javascript

    $(document).on( "ldapMailtoTypeRenderer", { }, function( event, params ) {
      var values = {
        "message": "Test",
        "value": params["value"]
      };
      params["render"] = renderTemplate("test_template", values);

      // you can also remove the value set by the default renderer by uncommenting next line
      //event.preventDefault();
    });

Then, you have to create the template (if you need one) in ``js-templates/test_template.html``:

.. code-block:: html

    <template id="test_template">
      <i>{message}</i>
      <div>{value}</div>
    </template>

