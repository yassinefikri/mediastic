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
}