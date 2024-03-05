import PropTypes from 'prop-types'

/**
 * Styled wrapper for an <input> element with a label and an optional reason for being requested
 */
export default function FormField({
    label,
    type,
    value,
    setValue,
    reason,
    required,
}) {
    return (
        <label>{label}: <input
            className='border border-black rounded-md bg-slate-50 focus:bg-slate-200'
            type={type}
            value={value}
            onChange={e => setValue(e.target.value)}
            required={required}
        />{reason ? <p className='text-sm text-gray-900'>{reason}</p> : <br/>}</label>
    )
}
FormField.propTypes = {
    label: PropTypes.string.isRequired,
    type: PropTypes.string.isRequired,
    value: PropTypes.string.isRequired,
    setValue: PropTypes.func.isRequired,
    reason: PropTypes.string,
    required: PropTypes.bool,
}
