"use client";

import { useParams } from "next/navigation";
import { useMessages } from "@/hooks/useMessages";
import MessageList from "@/components/chat/MessageList";
import MessageInput from "@/components/chat/MessageInput";

export default function ChatConversationPage() {
  const params = useParams();

  const conversationId = Number(params.conversationId);

  const { messages } = useMessages(conversationId);

  return (
    <div className="flex flex-col h-screen">
      <div className="flex-1 overflow-y-auto">
        <MessageList messages={messages} />
      </div>

      <MessageInput conversationId={conversationId} />
    </div>
  );
}
