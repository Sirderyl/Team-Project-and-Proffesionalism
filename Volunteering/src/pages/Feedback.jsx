import { Rating } from "@mui/material"

function Feedback() {
    return (
        <div>
            <h1 className="text-3xl font-bold mb-3 ml-5">Volunteering Feedback</h1>
            <p className="text-lg mt-6 ml-5">Rate your experience Clearing Tables with Middlesbrough Soup Kitchen:</p>       
            <p className="ml-5 mt-6">Clearing Tables</p><Rating className="ml-5"></Rating>
            <p className="text-lg mt-6 ml-5">Rate your experience volunteering with the following people:</p>  
            <div className="flex flex-col ml-5 mt-6">  
                    <p>John</p><Rating></Rating>
                    <p>Emma</p><Rating></Rating>
                    <p>Alex</p><Rating></Rating>
                </div>
            <div className="flex flex-row ml-5">
                <button className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-5">Submit Feedback</button>
                <button className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-5 ml-3">Skip</button>
            </div>
        </div>
    )
}

export default Feedback