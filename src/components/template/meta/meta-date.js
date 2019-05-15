/**
 * WordPress dependencies
 */
const { Component } = wp.element;
const {
	dateI18n,
	format,
	__experimentalGetSettings,
} = wp.date;

class MetaDate extends Component {
	render() {
		const {
			post,
		} = this.props;

		// eslint-disable-next-line no-restricted-syntax
		const dateFormat = __experimentalGetSettings().formats.date;

		return (
			<span className="tz-meta-date">
				<a href={ post.link } target="_blank" rel="noreferrer noopener">
					<time dateTime={ format( 'c', post.date_gmt ) } className="published updated">
						{ dateI18n( dateFormat, post.date_gmt ) }
					</time>
				</a>
			</span>
		);
	}
}

export default MetaDate;
