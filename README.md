
# TYPO3 Storybook Fluid API Extension [Backend]

This TYPO3 extension provides an API to render TYPO3 Fluid templates and partials through HTTP/HTTPS requests.
**Compatible with TYPO3 12.**

Inspired by the [Storybook TYPO3 Fluid plugin](https://github.com/philip-hartmann/storybook-typo3fluid), this extension extends the functionality to facilitate rendering and testing TYPO3 Fluid templates in external tools such as Storybook or other frontend environments.

[Learn More](https://github.com/CasianBlanaru/typo3fluid-storybook-plugin/tree/main/frontend)

---

## Features

- **Render Fluid Templates via API**: Seamlessly render TYPO3 Fluid templates and partials through JSON-based HTTP requests.
- **Rapid Frontend Development**: Connect TYPO3 templates with frontend tools like Storybook to test and preview components outside the TYPO3 environment.
- **JSON-Based Communication**: Simplified integration with frontend applications using JSON request and response formats.
- **TYPO3 v12 Compatible**: Fully compatible with TYPO3 12, leveraging the latest TYPO3 features.

---

## Requirements

- A working TYPO3 instance with TYPO3 v12 installed.
- TYPO3 site configured and accessible.

---

## Installation

Install the extension via Composer:

```bash
composer req pixelcoda/typo3-storybook-fluid-api
```

Clear the TYPO3 cache after installation to ensure the extension is loaded:

```bash
php typo3cms cache:flush
```

---

## Usage

### API Endpoint

The default API endpoint for rendering templates is:

```
/fluid/render
```

This endpoint accepts JSON payloads to specify templates, partials, and additional arguments.

---

### Request Structure

You can send a POST request to the endpoint with the following JSON payload:

```json
{
  "template": "Pages/Default",  /* Path to the Fluid template */
  "partial": "Atomics/Button",  /*  Optional path to a partial */
  "section": "content",         /* Optional section name */
  "arguments": {                /* Optional arguments for the template */
    "key": "value"
  }
}
```

---

### Response Structure

#### Success Response
```json
{
  "data": "<html>Rendered Template</html>",
  "error": ""
}
```

#### Error Response
```json
{
  "data": "",
  "error": "Template or partial not found."
}
```

---

## Example Workflow with Storybook

This example demonstrates how to integrate a TYPO3 Fluid template (`PersonsListTeaserFluid`) into Storybook for rendering and interactive customization.

### Fluid Template Import

The Fluid renderer is imported using the `FluidTemplate` function:

```javascript
import FluidTemplate from '../../../../.storybook/typo3FluidTemplates';
```

### Define the Fluid Template Path

Specify the path to the Fluid template:

```javascript
const PersonsListTeaserFluidpath = 'EXT:your_ext/Resources/Private/Partials/List/Item.html';
```

### Default Arguments

Define default values for the template variables:

```javascript
const defaultArgs = {
    fullName: 'Max Mustermann',
    image: 'https://placehold.co/400x400/cc006e/white',
    detailPage: '/detail-page',
    position: 'Professor',
    work: 'Lehrt Physik und Mathematik',
    officeHours: 'Mo-Fr 10-12 Uhr',
    telephone: '+49 30 12345678',
    room: 'B-123',
    email: 'max.mustermann@example.com',
};
```

### Storybook Configuration

The `PersonsListTeaserFluid` story is exported for use in Storybook:

```javascript
export default {
    title: 'Molecules/PersonsListTeaserFluid',
    parameters: {
        layout: 'centered',
    },
    argTypes: {
        fullName: { control: 'text', defaultValue: defaultArgs.fullName },
        image: { control: 'text', defaultValue: defaultArgs.image },
        detailPage: { control: 'text', defaultValue: defaultArgs.detailPage },
        position: { control: 'text', defaultValue: defaultArgs.position },
        work: { control: 'text', defaultValue: defaultArgs.work },
        officeHours: { control: 'text', defaultValue: defaultArgs.officeHours },
        telephone: { control: 'text', defaultValue: defaultArgs.telephone },
        room: { control: 'text', defaultValue: defaultArgs.room },
        email: { control: 'text', defaultValue: defaultArgs.email },
    },
};
```

### Define the Template

Create a template function that renders the Fluid template:

```javascript
const Template = (args) => {
    const html = FluidTemplate({
        templatePath: PersonsListTeaserFluidpath,
        variables: {
            person: {
                fullName: args.fullName,
                image: args.image,
                detailPage: args.detailPage,
                position: { title: args.position },
                work: args.work,
                officeHours: args.officeHours,
                telephone: args.telephone,
                room: args.room,
                email: args.email,
            },
        },
    });

    return `<div>${html}</div>`;
};
```

### Export the Story

The story is exported and connected to the default arguments:

```javascript
export const PersonsListTeaserFluid = Template.bind({});
PersonsListTeaserFluid.args = {
    ...defaultArgs,
};
```

---

## Benefits

- **Interactive Testing**: Test Fluid templates dynamically in Storybook.
- **Decoupled Development**: Render TYPO3 Fluid templates without a fully loaded TYPO3 environment.
- **Modern Workflow**: Enable modern component-based frontend development.

---

## Contributing

We welcome contributions to improve this extension. If you encounter bugs or have feature suggestions, feel free to open an issue or submit a pull request on GitHub.

---

## License

This extension is licensed under the [MIT License](LICENSE).

---

## Credits

Inspired by [storybook-typo3fluid](https://github.com/philip-hartmann/storybook-typo3fluid).
Developed by [Casian Blanaru / PixelCoda](https://pixelcoda.de).
