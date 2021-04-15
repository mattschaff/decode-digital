'use strict';

/* Create a new Fractal instance and export it for use elsewhere if required */
const fractal = module.exports = require('@frctl/fractal').create();

/** Set Twig Adapter */
const twigAdapter = require('@frctl/twig')();
fractal.components.engine(twigAdapter);
fractal.components.set('ext', '.twig');

/* Set the title of the project */
fractal.set('project.title', 'Decode Advertising Component Library');

/** Set status of all components. */
fractal.set('default.status', 'WIP');

/* Tell Fractal where the components will live */
fractal.components.set('path', __dirname + '/templates/components');

/** Set default preview, where we can apply CSS/JS. */
fractal.components.set('default.preview', '@preview');


/* Set the static HTML build destination */
fractal.web.set('builder.dest', __dirname + '../../../../component-library');