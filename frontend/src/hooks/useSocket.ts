import { useEffect, useRef } from "react";
import { ChatSocket } from "@/websocket/socket";

export function useSocket(token: string) {
  const socketRef = useRef<ChatSocket | null>(null);

  useEffect(() => {
    if (!token) return;

    socketRef.current = new ChatSocket(token);

    return () => {
      socketRef.current = null;
    };
  }, [token]);

  return socketRef;
}
