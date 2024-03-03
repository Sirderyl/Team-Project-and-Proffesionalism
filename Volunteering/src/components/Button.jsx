/**
 * Styled button component
 */
export default function Button(props) {
    return (
        <button
            className='border-2 border-black rounded-md bg-slate-50 hover:bg-slate-200 focus:bg-slate-200 px-0.5'
            {...props}
        />
    )
}
