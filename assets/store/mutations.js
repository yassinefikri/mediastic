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
    addMessages(state, payload) {
        let obj = {...state.messages}
        let messages = payload.data
        messages.forEach(function (message) {
            if (undefined === obj[message.conversation.id]) {
                obj[message.conversation.id] = []
            }
            message.seenBy.push(message.sender)
            let arr = [...obj[message.conversation.id]]
            if (true === payload.end) {
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
    addConversation(state, conversations) {
        let arr = [...state.conversations]
        arr = conversations.concat(arr)
        state.conversations = arr
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
    },
    updateMessage(state, message) {
        let obj = {...state.messages}
        let index = obj[message.conversation.id].findIndex((temp) => (temp.id === message.id))
        if (-1 !== index) {
            obj[message.conversation.id][index] = message
            state.messages = obj
        }
    },
    handleNotification(state, payload) {
        let arr = [...state.notifications]
        let notifications = payload.data
        notifications.forEach(function (notification) {
            let index = arr.findIndex((notif) => notif.id === notification.id)
            if (-1 === index) {
                if (false === payload.end) {
                    arr.unshift(notification)
                } else {
                    arr.push(notification)
                }
            } else {
                arr[index] = notification
            }
        })
        state.notifications = arr
    },
    removeNotification(state, id) {
        state.notifications = state.notifications.filter((notification) => id !== notification.id)
    }
}
