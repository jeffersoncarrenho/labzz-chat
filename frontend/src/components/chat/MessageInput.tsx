"use client";

import { useState } from "react";
import { api } from "@/services/api";

interface Props {
  conversationId: number;
}

export default function MessageInput({ conversationId }: Props) {
  const [message, setMessage] = useState("");

  async function sendMessage() {
    if (!message.trim()) return;

    await api.post("/messages", {
      conversation_id: conversationId,
      content: message,
    });

    setMessage("");
  }

  return (
    <div className="flex border-t p-3">
      <input
        className="flex-1 border p-2 rounded-md"
        value={message}
        onChange={(e) => setMessage(e.target.value)}
        placeholder="Type a message..."
      />

      <button onClick={sendMessage} className="ml-2 px-4">
        Send
      </button>
    </div>
  );
}
