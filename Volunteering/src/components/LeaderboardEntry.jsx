import PropTypes from 'prop-types'

export default function LeaderboardEntry({ position, name, stats }) {
    var bgColour = "bg-white"
    var textColour = "text-gray-700"
    var fontWeight = "font-normal"
    if (position === 1) {
        bgColour = "bg-blue-600"
        textColour = "text-white"
        fontWeight = "font-bold"
    } else if (position === 2) {
        bgColour = "bg-blue-500"
        textColour = "text-white"
        fontWeight = "font-semibold"
    } else if (position === 3) {
        bgColour = "bg-blue-400"
        textColour = "text-white"
        fontWeight = "font-semibold"
    }

    return (
        <li className={`${bgColour} ${textColour} ${fontWeight} list-none rounded-lg shadow-md p-3 m-2 flex justify-between items-center`}>
            <span>{position} </span>
            <span>{name} </span>
            <span>{stats} </span>
        </li>
    );
}

LeaderboardEntry.propTypes = {
    position: PropTypes.number.isRequired,
    name: PropTypes.string.isRequired,
    stats: PropTypes.number.isRequired
}
