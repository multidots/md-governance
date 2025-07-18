const fs = require( 'fs' );
const path = require( 'path' );

const camelCase = require( 'camelcase' );
const chalk = require( 'chalk' );
const fuzzy = require( 'fuzzy' );
const inquirer = require( 'inquirer' );
const inquirerPrompt = require( 'inquirer-autocomplete-prompt' );
const makeDir = require( 'make-dir' );
const slugify = require( 'slugify' );
const { render } = require( 'mustache' );

const dashIcons = require( './dashicons.js' );

const blockBaseDir = path.normalize( process.cwd() + '/src/blocks/' );

const getTemplate = ( templateName ) => {
	const templatePath = path.normalize(
		process.cwd() + '/bin/template/' + templateName
	);
	return fs.readFileSync( templatePath, { encoding: 'utf8' } );
};

/**
 * Unslugify a string.
 *
 * @param {string} slug slug to unslugify.
 * @return {string} unslugified string.
 */
const unslugify = ( slug ) => {
	const result = slug.replace( /\-/g, ' ' );
	return result.replace( /\w\S*/g, function ( txt ) {
		return txt.charAt( 0 ).toUpperCase() + txt.slice( 1 ).toLowerCase();
	} );
};

// Self invoking async function as a wrapper.
( async () => {
	// Enable autocomplete.
	inquirer.registerPrompt( 'autocomplete', inquirerPrompt );

	const { description, icon, name, slug } = await inquirer.prompt( [
		{
			type: 'input',
			name: 'name',
			message:
				'What is the name of the block, as it would appear in the block selector?',
			validate( input ) {
				// Required a value with a first letter, spaces, numbers, and hyphens only.
				if ( ! /^[A-Z]+[A-Za-z0-9\- ]*$/.test( input ) ) {
					return 'Name must start with a capital letter and can only letters, numbers, and hyphens.';
				} else if ( ! input ) {
					return 'A block name is required.';
				}

				return true;
			},
		},
		{
			type: 'input',
			name: 'slug',
			// Default is the slugified name.
			default( answers ) {
				return slugify( answers.name ).toLowerCase();
			},
			message: 'What is the slug of the block?',
			validate( input ) {
				// Only allow lowercase letters, numbers, and hyphens.
				if ( ! /^[a-z0-9\- ]+$/.test( input ) ) {
					return 'Slug can only container lowercase letters, numbers and hyphens.';
				}

				return true;
			},
			// Auto-replace spaces with hyphens.
			transformer( input ) {
				// Transform input, input.replace( /\s+/g, '-' ).toLowerCase()
				return input;
			},
			// Only ask if the block has a given name.
			when( answers ) {
				return !! answers.name;
			},
		},
		{
			type: 'input',
			name: 'description',
			default: false,
			message: 'What is a description of the block?',
		},
		{
			type: 'autocomplete',
			name: 'icon',
			message: 'What dashicon should be used?',
			default: 'smiley',
			/**
			 * Callback for autocomplete for icons.
			 *
			 * @param {Object} _answers answers thus far.
			 * @param {string} input    current input.
			 * @return {Array} array of values.
			 */
			source: ( _answers, input = '' ) => {
				return new Promise( ( resolve ) => {
					setTimeout( () => {
						resolve(
							fuzzy
								.filter( input, dashIcons.default )
								.map( ( el ) => el.original )
						);
					}, Math.random() * 470 + 30 );
				} );
			},
		},
	] );

	// Return slug or slugified name.
	const modifiedSlug = slug || slugify( name ).toLowerCase();
	const phpName = unslugify( modifiedSlug );

	const view = {
		blockName: camelCase( name ),
		description,
		icon,
		name,
		phpName: slugify( phpName, '_' ),
		slug: modifiedSlug,
	};

	if ( fs.existsSync( blockBaseDir + slug ) ) {
		console.log(
			chalk.red(
				"Can't create ",
				chalk.underline.bold( name ),
				'. Please remove ',
				blockBaseDir + slug,
				' to try again'
			)
		);
		process.exit( 1 );
	}

	// Base location for block data.
	const blockBase = blockBaseDir + slug;

	// makeDir is recursive, so making the test sets it all up
	await makeDir( blockBase );

	const jsonTemplate = getTemplate( 'block.json.mustache' );
	const phpTemplate = getTemplate( 'block.php.mustache' );
	const editTemplate = getTemplate( 'edit.js.mustache' );
	const mainTemplate = getTemplate( 'index.js.mustache' );
	const saveTemplate = getTemplate( 'save.js.mustache' );
	const styleTemplate = getTemplate( 'style.scss.mustache' );

	const jsonFile = blockBase + '/block.json';
	const phpFile = blockBase + '/class-' + slug + '.php';
	const editFile = blockBase + '/edit.js';
	const mainFile = blockBase + '/index.js';
	const saveFile = blockBase + '/save.js';
	const mainStyleFile = blockBase + '/style.scss';
	const editorStyleFile = blockBase + '/editor.scss';

	try {
		fs.writeFileSync( jsonFile, render( jsonTemplate, view ), 'utf8' );
		fs.writeFileSync( phpFile, render( phpTemplate, view ), 'utf8' );
		fs.writeFileSync( editFile, render( editTemplate, view ), 'utf8' );
		fs.writeFileSync( mainFile, render( mainTemplate, view ), 'utf8' );
		fs.writeFileSync( saveFile, render( saveTemplate, view ), 'utf8' );
		fs.writeFileSync(
			mainStyleFile,
			render( styleTemplate, view ),
			'utf8'
		);
		fs.writeFileSync(
			editorStyleFile,
			render( styleTemplate, view ),
			'utf8'
		);

		console.log(
			chalk.green(
				'Scaffold for',
				chalk.underline.bold( name ),
				'has been setup'
			)
		);
	} catch ( e ) {
		console.log( chalk.red( 'ERROR' ) );
		console.log( e );
	}
} )();
