import ConversationList from "@/components/chat/ConversationList";

export default function ChatPage() {
  return (
    <div className="flex h-screen">
      <div className="w-80 border-r">
        <ConversationList />
      </div>

      <div className="flex-1 flex items-center justify-center">
        Select a conversation
      </div>
    </div>
  );
}
