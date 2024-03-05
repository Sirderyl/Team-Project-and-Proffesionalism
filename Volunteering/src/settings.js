/**
 * Settings for the app
 */

// Use an external API, unless we're running on localhost
const isLocalHost = window.location.hostname === 'localhost'

export const apiRoot = isLocalHost ? 'https://w20013000.nuwebspace.co.uk/api' : '/api'
