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
    allConversations(state) {
        return state.conversations
    },
}