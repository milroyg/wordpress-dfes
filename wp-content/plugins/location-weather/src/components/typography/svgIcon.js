export const HeadingIcon = () => (
	<svg
		xmlns="http://www.w3.org/2000/svg"
		width={ 17 }
		height={ 16 }
		fill="none"
	>
		<g>
			<rect
				width={ 16 }
				height={ 1.5 }
				x={ 0.5 }
				fill="#2F2F2F"
				rx={ 0.75 }
			/>
			<rect
				width={ 16 }
				height={ 1.5 }
				x={ 0.5 }
				y={ 14.5 }
				fill="#2F2F2F"
				rx={ 0.75 }
			/>
			<path
				stroke="#2F2F2F"
				strokeLinecap="round"
				strokeLinejoin="round"
				strokeWidth={ 1.5 }
				d="m4.75 12.5 2.763-8.288a1.04 1.04 0 0 1 1.974 0L12.25 12.5m-6.5-3h5.5"
			/>
		</g>
		<defs>
			<clipPath id="a">
				<path fill="#fff" d="M.5 0h16v16H.5z" />
			</clipPath>
		</defs>
	</svg>
);

export const DecorationUnderline = () => {
	return (
		<svg
			xmlns="http://www.w3.org/2000/svg"
			width={ 25 }
			height={ 24 }
			fill="none"
		>
			<path
				fill="#757575"
				d="M12.25 17c3.3 0 6-2.7 6-6V3h-2.5v8c0 1.95-1.55 3.5-3.5 3.5s-3.5-1.55-3.5-3.5V3h-2.5v8c0 3.3 2.7 6 6 6Zm-7 2v2h14v-2h-14Z"
			/>
		</svg>
	);
};

export const DecorationLineThrough = () => {
	return (
		<svg
			xmlns="http://www.w3.org/2000/svg"
			width={ 25 }
			height={ 24 }
			fill="none"
		>
			<path
				fill="#757575"
				fillRule="evenodd"
				d="M12.154 19c-.795 0-1.603-.09-2.423-.27-.82-.166-1.481-.365-1.981-.595L8 16.192c.641.308 1.365.577 2.173.808.808.23 1.61.346 2.404.346.897 0 1.59-.16 2.077-.48.5-.334.75-.828.75-1.481 0-.488-.128-.891-.385-1.212-.243-.333-.609-.609-1.096-.827a9.075 9.075 0 0 0-.83-.346H4.75v-1h16v1h-4.094c.499.587.748 1.337.748 2.25 0 1.18-.442 2.103-1.327 2.77-.885.653-2.192.98-3.923.98ZM7.84 9a4.884 4.884 0 0 1-.013-.365c0-1.09.417-1.968 1.25-2.635.846-.667 2.032-1 3.558-1 .82 0 1.596.09 2.326.27.744.179 1.392.397 1.943.653l-.192 1.942c-.718-.423-1.41-.73-2.077-.923a7.456 7.456 0 0 0-2.077-.288c-.834 0-1.5.154-2 .461-.488.308-.731.782-.731 1.423 0 .165.016.32.047.462H7.84Z"
				clipRule="evenodd"
			/>
		</svg>
	);
};

export const DecorationOverLine = () => {
	return (
		<svg
			xmlns="http://www.w3.org/2000/svg"
			width={ 25 }
			height={ 24 }
			fill="none"
		>
			<path
				fill="#757575"
				d="M12.25 21c3.3 0 6-2.7 6-6V7h-2.5v8c0 1.95-1.55 3.5-3.5 3.5s-3.5-1.55-3.5-3.5V7h-2.5v8c0 3.3 2.7 6 6 6ZM5.25 3v2h14V3h-14Z"
			/>
		</svg>
	);
};

// TypographyIcon Icon.
export const TypographyIcon = () => (
	<svg
		xmlns="http://www.w3.org/2000/svg"
		width={ 10 }
		height={ 12 }
		fill="none"
	>
		<path d="M7.577 11.256H2.409v-.608c.458 0 .8-.032 1.024-.096a.635.635 0 0 0 .432-.464c.074-.235.112-.592.112-1.072v-7.44h-.832c-.587 0-1.03.064-1.328.192-.288.128-.496.357-.624.688-.128.32-.25.768-.368 1.344H.137L.313.744H9.72L9.865 3.8h-.688c-.118-.576-.246-1.024-.384-1.344-.128-.33-.336-.56-.624-.688-.288-.128-.731-.192-1.328-.192h-.816v7.44c0 .48.037.837.112 1.072.074.235.218.39.432.464.224.064.56.096 1.008.096v.608Z" />
	</svg>
);

export const ItalicIcon = () => (
	<svg
		xmlns="http://www.w3.org/2000/svg"
		width={ 25 }
		height={ 24 }
		fill="none"
	>
		<path
			fill="#757575"
			d="M12.358 17.5h-1.911l2.248-10.569h1.912L12.358 17.5Z"
		/>
	</svg>
);
