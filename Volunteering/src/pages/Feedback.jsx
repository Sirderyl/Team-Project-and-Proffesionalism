import { Rating } from "@mui/material"
import { apiRoot } from '../settings'
import {useState, useEffect} from 'react';
import {Link} from 'react-router-dom';
import axios from 'axios';
import PropTypes from 'prop-types'

export default function Feedback({user}) {
    const [activityRating, setActivityRating] = useState();
    const [activity, setActivity] = useState();

    useEffect(() => {
        const fetchActivity = async () => {
            try {
                const response = await axios.get('https://w21017158.nuwebspace.co.uk/api/activity/1');
                setActivity(response.data);
            } catch (error) {
                console.error('Error fetching activity:', error);
            }
        };

        fetchActivity();
    }, []);


    const handleSubmitFeedback = async () => {
        console.log(activityRating);
        try {
            await axios.post('https://w21017158.nuwebspace.co.uk/api/userSchedule/rating', null, {
                params: {
                    id: 1,
                    rating: activityRating
                }
            });
        } catch (error) {
            console.error('Error submitting feedback:', error);
        }
    };
    
    if(user.isManager) {
        return (
            <div>
                <h1 className="text-3xl font-bold mb-3 ml-5">Managers are not allowed to rate activities</h1>
                <Link to='/'>
                    <button className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-5 ml-5">Back</button>
                </Link>
            </div>
        )
    }
    return (
        <div>
            
            <h1 className="text-3xl font-bold mb-3 ml-5">Volunteering Feedback</h1>
            { <img className="w-40 h-40 ml-5" src={`${apiRoot}/activity/1/previewimage`}/>}
            {activity && (
            <p className="text-lg mt-6 ml-5">Rate your experience doing the activity, {activity.name} with the organization, {activity.organization.name}:</p>       
            )}
            {activity && (
            <p className="ml-5 mt-6">{activity.name}</p>
            )}
            <Rating className="ml-5" value={activityRating ? activityRating : null} onChange={e => setActivityRating(parseInt(e.target.value))}></Rating>
            
            <p className="text-lg mt-6 ml-5">Rate your experience volunteering with the following people:</p>  
            <div className="flex flex-col ml-5 mt-6">  
                    <p>John</p><Rating></Rating>
                    <p>Emma</p><Rating></Rating>
                    <p>Alex</p><Rating></Rating>
                </div>
            <div className="flex flex-row ml-5">
                <button className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-5" onClick={handleSubmitFeedback}>Submit Feedback</button>
                <button className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-5 ml-3">Back</button>
            </div>
            
        </div>
    )
}

Feedback.propTypes = {user: PropTypes.object.isRequired}