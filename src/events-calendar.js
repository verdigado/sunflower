import { registerBlockType } from '@wordpress/blocks';

registerBlockType( 'sunflower/events-calendar', {
    title: 'Terminkalender',
    description: 'Tabellarische Kalenderansicht mit Terminen.',
    icon: 'calendar-alt',
    category: 'sunflower-blocks',
    edit: () => <div>Terminkalender (Vorschau leider nicht möglich)</div>
} );
