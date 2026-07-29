import './editor.scss';
const Divider = ( { position = '' } ) => {
	return <span className={ `splw-divider ${ position }` }></span>;
};

export default Divider;
