import { useEffect, useState } from "react";
import { getMessages } from "@/services/messages";

import { Message } from "@/types/message";

export function useMessages(conversationId: number) {
  const [messages, setMessages] = useState<Message[]>([]);

  useEffect(() => {
    async function loadMessages() {
      const data = await getMessages(conversationId);

      setMessages(data);
    }

    loadMessages();
  }, [conversationId]);

  return {
    messages,
    setMessages,
  };
}
