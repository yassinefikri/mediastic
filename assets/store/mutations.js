export default {
    setUserInfos(state, userInfos) {
        state.userInfos = userInfos
    },
    setAlert(state, alert) {
        state.alert = alert
    },
    deleteAlert(state) {
        state.alert = null
    },
    addFriendships(state, friendships) {
        this.commit('addToObject', {object: 'friendships', data: friendships, key: 'id'})
    },
    removeFriendship(state, friendship) {
        this.commit('removeFromObject', {object: 'friendships', data: friendship, key: 'id'})
    },
    addFriend(state, friends) {
        this.commit('addToObject', {object: 'friends', data: friends, key: 'username'})
    },
    removeFriend(state, friend) {
        this.commit('removeFromObject', {object: 'friends', data: friend, key: 'username'})
    },
    addMessages(state, messages) {
        let obj = {...state.messages}
        messages.forEach(function (message) {
            if (undefined === obj[message.conversation.id]) {
                obj[message.conversation.id] = []
            }
            message.seenBy.push(message.sender)
            let arr = [...obj[message.conversation.id]]
            if (messages.length === 1) {
                arr.push(message)
            } else {
                arr.unshift(message)
            }
            obj[message.conversation.id] = arr
        })
        state.messages = obj
        this.commit('setSeens', messages)
    },
    setSeens(state, messages) {
        let obj = {...state.lastSeenMessage}
        messages.forEach(function (message) {
            if (undefined === obj[message.conversation.id]) {
                obj[message.conversation.id] = {}
            }
            message.seenBy.forEach(function (person) {
                if (undefined === obj[message.conversation.id][person.username] || obj[message.conversation.id][person.username] < message.id) {
                    obj[message.conversation.id][person.username] = message.id
                }
            })
        })
        state.lastSeenMessage = obj
    },
    removeMessage(state, messages) {
        this.commit('removeFromObject', {object: 'messages', data: messages, key: 'id'})
    },
    addConversation(state, conversations) {
        let arr = [...state.conversations]
        arr = conversations.concat(arr)
        state.conversations = arr
    },
    removeConversation(state, specificConversation) {
        state.conversations = state.conversations.filter((conversation) => conversation.id !== specificConversation.id)
    },
    updateOrAddConversation(state, specificConversation) {
        let index = state.conversations.findIndex((conversation) => conversation.id === specificConversation.id)
        let arr = [...state.conversations]
        if (-1 === index) {
            arr.unshift(specificConversation)
        } else {
            arr[index] = specificConversation
        }
        state.conversations = arr
    },
    moveConversationToStart(state, otherConversation) {
        let index = state.conversations.findIndex((conversation) => conversation.id === otherConversation.id)
        let arr = [...state.conversations]
        let fullConversation = arr.splice(index, 1)[0]
        fullConversation.updatedAt = otherConversation.updatedAt
        arr.unshift(fullConversation)
        state.conversations = arr
    },
    addToObject(state, payload) {
        let obj = {...state[payload.object]}
        payload.data.forEach(function (item) {
            Object.assign(obj, {[item[payload.key]]: item})
        })
        state[payload.object] = obj
    },
    removeFromObject(state, payload) {
        let obj = {...state[payload.object]}
        delete obj[payload.data[payload.key]]
        state[payload.object] = obj
    },
    resetObject(state, payload) {
        state[payload.name] = {}
    },
    addUnreadConversation(state, conversation) {
        let obj = {...state.unreadConversations}
        if (undefined === obj[conversation.id]) {
            obj[conversation.id] = 1
        } else {
            obj[conversation.id]++
        }
        state.unreadConversations = obj
    },
    setUnreadConversation(state, value) {
        let obj = {...state.unreadConversations}
        value.forEach(function (conversation) {
            obj[conversation.id] = conversation.count
        })
        state.unreadConversations = obj
    },
    resetUnreadConversation(state, conversationId) {
        let obj = {...state.unreadConversations}
        delete obj[conversationId]
        state.unreadConversations = obj
    },
    incrementUnreadNotificationsCount(state) {
        state.unreadNotificationsCount++
    },
    setUnreadNotificationsCount(state, value) {
        state.unreadNotificationsCount = value
    },
    resetUnreadNotificationsCount(state) {
        state.unreadNotificationsCount = 0
    },
    setMessageSeen(state, seens) {
        let obj = {...state.lastSeenMessage}
        let obj2 = {...state.messages}
        for (const [conversation, seen] of Object.entries(seens)) {
            if (undefined === obj[conversation]) {
                obj[conversation] = {}
            }
            seen.forEach(function (temp) {
                let user = JSON.parse(temp.user)
                obj[conversation][user.username] = temp.message
                if (undefined !== obj2[conversation]) {
                    let index = obj2[conversation].findIndex((message) => message.id === temp.message)
                    obj2[conversation][index].seenBy.push(user)
                }
            })
        }
        state.lastSeenMessage = obj
        state.messages = obj2
    },
    initMessages(state, conversationId) {
        let obj = {...state.messages}
        delete obj[conversationId]
        state.messages = obj
        obj = {...state.lastSeenMessage}
        delete obj[conversationId]
        state.lastSeenMessage = obj
    }
}