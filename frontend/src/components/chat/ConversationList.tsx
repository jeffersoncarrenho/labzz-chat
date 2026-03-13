"use client";

import { useEffect, useState } from "react";
import { getConversations } from "@/services/conversations";
import { Conversation } from "@/types/conversation";
import { useRouter } from "next/navigation";

export default function ConversationList() {
  const [conversations, setConversations] = useState<Conversation[]>([]);

  const router = useRouter();

  useEffect(() => {
    async function load() {
      const data = await getConversations(1);

      setConversations(data);
    }

    load();
  }, []);

  return (
    <div>
      {conversations.map((conversation) => (
        <div
          key={conversation.id}
          className="p-3 border-b cursor-pointer hover:bg-gray-100"
          onClick={() => router.push(`/chat/${conversation.id}`)}
        >
          Conversation {conversation.id}
        </div>
      ))}
    </div>
  );
}
