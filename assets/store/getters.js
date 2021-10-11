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
    conversation: (state) => (id) => {
        return state.conversations.filter((conversation) => (conversation.id = id))[0] ?? null
    },
    alert(state) {
        return state.alert
    },
    messages       : (state) => (id) => {
        return state.messages[id]
    },
    lastSeenMessage: (state) => (id) => {
        return state.lastSeenMessage[id]
    },
    friendships(state) {
        return state.friendships
    },
    notifications(state) {
        return state.notifications
    },
    unreadNotificationsCount(state) {
        let count = 0
        for (const id in state.notifications) {
            if(false === state.notifications[id].seen) {
                count++
            }
        }
        return count
    }
}
