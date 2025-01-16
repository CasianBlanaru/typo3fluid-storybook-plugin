
# TYPO3 Storybook Fluid API

This TYPO3 extension provides an API to render TYPO3 Fluid templates and partials through HTTP/HTTPS requests.
**Compatible with TYPO3 12.**

Inspired by the [Storybook TYPO3 Fluid plugin](https://github.com/philip-hartmann/storybook-typo3fluid), this extension extends the functionality to facilitate rendering and testing TYPO3 Fluid templates in external tools such as Storybook or other frontend environments.

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
  "template": "Pages/Default", // Path to the Fluid template
  "partial": "Atomics/Button", // Optional path to a partial
  "section": "content",        // Optional section name
  "arguments": {               // Optional arguments for the template
    "key": "value"
  }
}
```

---

### Response Structure

The API will return the rendered output or an error message:

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

1. Install [Storybook](https://storybook.js.org/) in your frontend project.
2. Configure the TYPO3 Storybook Fluid API as a data source for your Storybook stories.
3. Use the API to fetch and render Fluid templates or components within your Storybook environment.
4. Pass template arguments dynamically to test your Fluid components.

Example usage in Storybook:

```javascript
export const Button = async () => {
  const response = await fetch('/fluid/render', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      partial: 'Atomics/Button',
      arguments: { label: 'Click Me', variant: 'primary' }
    }),
  });

  const { data } = await response.json();
  return data;
};
```

---

## Configuration Options

You can customize the API behavior via TYPO3 settings:

- **Endpoint Path**: Change the default `/fluid/render` endpoint in your extension's configuration.
- **Access Control**: Restrict API usage with authentication or IP whitelisting.
- **Debug Mode**: Enable detailed error messages for debugging during development.

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
