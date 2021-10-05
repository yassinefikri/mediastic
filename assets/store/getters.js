export default {
    username(state) {
        return state.userInfos['username']
    },
    avatar(state) {
        return state.userInfos['avatar_url']
    },
    cover(state) {
        return state.userInfos['cover_url']
    },
    firstname(state) {
        return state.userInfos['firstName']
    },
    userInfos(state) {
        return state.userInfos
    },
    unreadConversations(state) {
        return state.unreadConversations
    },
    allConversations(state) {
        return state.conversations
    },
    alert(state) {
        return state.alert
    },
    messages: (state) => (id) => {
        return state.messages[id]
    },
    lastSeenMessage: (state) => (id) => {
        return state.lastSeenMessage[id]
    },
    friendships(state) {
        return state.friendships
    },
    unreadNotificationsCount(state) {
        return state.unreadNotificationsCount
    }
}
